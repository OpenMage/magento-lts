<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Mage\Oauth\Model;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Oauth_Model_Consumer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ConsumerTest extends OpenMageTest
{
    // @phpstan-ignore property.onlyWritten
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/consumer');
    }

    /**
     * @dataProvider validateDataProvider
     * @group Model
     */
    public function testValidate(bool|string $expected, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);

        try {
            self::assertTrue($mock->validate());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expected, $mageCoreException->getMessage());
        }
    }

    public function validateDataProvider(): Generator
    {
        $validData = [
            'setKey'    => str_repeat('x', 32),
            'setSecret' => str_repeat('x', 32),
        ];

        $error = 'This value should have exactly 32 characters.';

        yield 'valid' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['setKey'] = str_repeat('x', 3);
        yield 'invalid to short key' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setKey'] = str_repeat('x', 33);
        yield 'invalid to long key' => [
            $error,
            $data,
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
    }
}
