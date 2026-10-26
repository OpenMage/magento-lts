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
     * @param array<string, mixed> $data
     */
    public function testValidate(bool|string $expected, array $data): void
    {
        self::$subject->setData($data);

        if (is_string($expected)) {
            self::expectExceptionMessage($expected);
        }

        self::assertTrue(self::$subject->validate());
    }

    /**
     * @return Generator<string, list{bool|string, array<string, mixed>}, void, void>
     */
    public static function validateDataProvider(): Generator
    {
        $validData = [
            'consumer_id'  => '1',
            'callback_url' => 'https://example.com/callback',
            'secret'       => str_repeat('x', 32),
            'token'        => str_repeat('x', 32),
            'verifier'     => str_repeat('x', 32),
        ];

        $error = 'This value should have exactly 32 characters.';

        yield 'valid' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['secret'] = str_repeat('x', 3);
        yield 'invalid to short secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['secret'] = str_repeat('x', 33);
        yield 'invalid to long secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['token'] = str_repeat('x', 3);
        yield 'invalid to short token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['token'] = str_repeat('x', 33);
        yield 'invalid to long token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['callback_url'] = 'invalid-url';
        yield 'invalid url' => [
            'Invalid URL "invalid-url".',
            $data,
        ];

        $data = $validData;
        $data['consumer'] = (new Consumer())->setCallbackUrl('https://legit-app.com');
        $data['callback_url'] = 'https://legit-app.com/cb';
        yield 'url covered by the consumer callback url' => [
            true,
            $data,
        ];

        $data = $validData;
        $data['consumer'] = (new Consumer())->setCallbackUrl('myapp://callback');
        $data['callback_url'] = 'myapp://callback';
        yield 'custom scheme covered by the consumer callback url' => [
            true,
            $data,
        ];

        $data = $validData;
        $data['consumer'] = (new Consumer())->setCallbackUrl('https://legit-app.com');
        $data['callback_url'] = 'https://legit-app.com.evil.com/cb';
        yield 'url not covered by the consumer callback url' => [
            'Invalid URL "https://legit-app.com.evil.com/cb".',
            $data,
        ];
    }
}
