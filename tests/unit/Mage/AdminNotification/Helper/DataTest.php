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

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Helper;

use Mage;
use Mage_AdminNotification_Helper_Data as Subject;
use Mage_AdminNotification_Model_Inbox;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::helper('adminnotification/data');
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetLatestNotice(): void
    {
        static::assertInstanceOf(Mage_AdminNotification_Model_Inbox::class, self::$subject->getLatestNotice());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetUnreadNoticeCount(): void
    {
        static::assertIsInt(self::$subject->getUnreadNoticeCount(99));
    }

    /**
     * @covers Mage_AdminNotification_Helper_Data::getPopupObjectUrl()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetPopupObjectUrl(): void
    {
        static::assertSame('', self::$subject->getPopupObjectUrl());
    }
}
