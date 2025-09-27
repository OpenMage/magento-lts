<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Helper;

use Mage;
use Mage_AdminNotification_Helper_Data as Subject;
use Mage_AdminNotification_Model_Inbox;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminnotification/data');
    }

    /**
     * @group Helper
     */
    public function testGetLatestNotice(): void
    {
        static::assertInstanceOf(Mage_AdminNotification_Model_Inbox::class, self::$subject->getLatestNotice());
    }

    /**
     * @group Helper
     */
    public function testGetUnreadNoticeCount(): void
    {
        static::assertIsInt(self::$subject->getUnreadNoticeCount(99));
    }

    /**
     * @covers Mage_AdminNotification_Helper_Data::getPopupObjectUrl()
     * @group Helper
     */
    public function testGetPopupObjectUrl(): void
    {
        static::assertSame('', self::$subject->getPopupObjectUrl());
    }
}
