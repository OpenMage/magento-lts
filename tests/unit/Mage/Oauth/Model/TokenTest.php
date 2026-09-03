<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Mage\Oauth\Model;

use Override;
use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Oauth_Model_Consumer as Consumer;
use Mage_Oauth_Model_Token as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class TokenTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/token');
    }

    /**
     * @dataProvider validateDataProvider
     * @group Model
     * @param array<string, mixed> $methods
     */
    public function testValidate(bool|string $expected, array $methods): void
    {
        self::$subject->setConsumerId($methods['setConsumerId']);
        self::$subject->setCallbackUrl($methods['setCallbackUrl']);
        self::$subject->setSecret($methods['setSecret']);
        self::$subject->setToken($methods['setToken']);
        self::$subject->setVerifier($methods['setVerifier']);
        if (isset($methods['setConsumer'])) {
            self::$subject->setConsumer($methods['setConsumer']);
        }

        try {
            self::assertTrue(self::$subject->validate());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expected, $mageCoreException->getMessage());
        } finally {
            if (isset($methods['setConsumer'])) {
                self::$subject->unsetData('consumer');
            }
        }
    }

    public function validateDataProvider(): Generator
    {
        $validData = [
            'setConsumerId'     => '1',
            'setCallbackUrl'    => 'https://example.com/callback',
            'setSecret'         => str_repeat('x', 32),
            'setToken'          => str_repeat('x', 32),
            'setVerifier'       => str_repeat('x', 32),
        ];

        $error = 'This value should have exactly 32 characters.';

        yield 'valid' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 3);
        yield 'invalid to short secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 33);
        yield 'invalid to long secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setToken'] = str_repeat('x', 3);
        yield 'invalid to short token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setToken'] = str_repeat('x', 33);
        yield 'invalid to long token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setCallbackUrl'] = 'invalid-url';
        yield 'invalid url' => [
            'Invalid URL "invalid-url".',
            $data,
        ];

        $data = $validData;
        $data['setConsumer'] = (new Consumer())->setCallbackUrl('https://legit-app.com');
        $data['setCallbackUrl'] = 'https://legit-app.com/cb';
        yield 'url covered by the consumer callback url' => [
            true,
            $data,
        ];

        $data = $validData;
        $data['setConsumer'] = (new Consumer())->setCallbackUrl('myapp://callback');
        $data['setCallbackUrl'] = 'myapp://callback';
        yield 'custom scheme covered by the consumer callback url' => [
            true,
            $data,
        ];

        $data = $validData;
        $data['setConsumer'] = (new Consumer())->setCallbackUrl('https://legit-app.com');
        $data['setCallbackUrl'] = 'https://legit-app.com.evil.com/cb';
        yield 'url not covered by the consumer callback url' => [
            'Invalid URL "https://legit-app.com.evil.com/cb".',
            $data,
        ];
    }
}
