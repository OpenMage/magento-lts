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
use Mage_Adminhtml_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/data');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getUrl()
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUrl(): void
    {
        static::assertIsString(self::$subject->getUrl());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::getCurrentUserId()
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetCurrentUserId(): void
    {
        static::assertFalse(self::$subject->getCurrentUserId());
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::prepareFilterString()
     * @group Helper
     */
    public function testPrepareFilterString(): void
    {
        static::assertIsArray(self::$subject->prepareFilterString(''));
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::decodeFilter()
     * @group Helper
     */
    public function testDecodeFilter(): void
    {
        $string = '';
        self::$subject->decodeFilter($string);
        static::assertSame('', $string);
    }

    /**
     * @covers Mage_Adminhtml_Helper_Data::isEnabledSecurityKeyUrl()
     * @group Helper
     */
    public function testIsEnabledSecurityKeyUrl(): void
    {
        static::assertTrue(self::$subject->isEnabledSecurityKeyUrl());
    }
}
