<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Helper;

use Mage;
use Mage_AdminNotification_Helper_Data as Subject;
use Mage_AdminNotification_Model_Inbox;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminnotification/data');
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetLatestNotice(): void
    {
        $this->assertInstanceOf(Mage_AdminNotification_Model_Inbox::class, $this->subject->getLatestNotice());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetUnreadNoticeCount(): void
    {
        $this->assertIsInt($this->subject->getUnreadNoticeCount(99));
    }

    /**
     * @covers Mage_AdminNotification_Helper_Data::getPopupObjectUrl()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Helper
     */
    public function testGetPopupObjectUrl(): void
    {
        $this->assertSame('', $this->subject->getPopupObjectUrl());
    }
}
