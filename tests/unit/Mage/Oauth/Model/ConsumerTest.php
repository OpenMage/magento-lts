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
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Model\ConsumerTrait;

final class ConsumerTest extends OpenMageTest
{
    use ConsumerTrait;

    // @phpstan-ignore property.onlyWritten
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/consumer');
    }

    /**
     * @dataProvider provideValidateData
     * @group Model
     * @param array<string, string> $methods
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
}
