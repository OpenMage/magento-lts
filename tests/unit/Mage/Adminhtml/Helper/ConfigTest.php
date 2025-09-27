<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper\ConfigTrait;

final class ConfigTest extends OpenMageTest
{
    use ConfigTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/config');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Config::getInputTypes()
     * @dataProvider provideGetInputTypes
     * @group Helper
     */
    public function testGetInputTypes(array $expectedResult, ?string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getInputTypes($inputType));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Config::getBackendModelByInputType()
     * @dataProvider provideGetBackendModelByInputType
     * @group Helper
     */
    public function testGetBackendModelByInputType(?string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getBackendModelByInputType($inputType));
    }
}
