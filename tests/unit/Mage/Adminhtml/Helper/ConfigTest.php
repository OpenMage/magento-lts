<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Mage;
use Mage_Adminhtml_Helper_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper\ConfigTrait;

class ConfigTest extends OpenMageTest
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
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetInputTypes(array $expectedResult, ?string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getInputTypes($inputType));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Config::getBackendModelByInputType()
     * @dataProvider provideGetBackendModelByInputType
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testGetBackendModelByInputType(?string $expectedResult, string $inputType): void
    {
        static::assertSame($expectedResult, self::$subject->getBackendModelByInputType($inputType));
    }
}
