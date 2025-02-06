<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Model;

use Mage;
use Mage_AdminNotification_Model_Feed as Subject;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class FeedTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('adminnotification/feed');
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedUrl(): void
    {
        $this->assertIsString($this->subject->getFeedUrl());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testCheckUpdate(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->checkUpdate());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedData(): void
    {
        $this->assertInstanceOf(SimpleXMLElement::class, $this->subject->getFeedData());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedXml(): void
    {
        $this->assertInstanceOf(SimpleXMLElement::class, $this->subject->getFeedXml());
    }
}
