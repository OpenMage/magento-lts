<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_AdminNotification
 * @group Mage_AdminNotification_Model
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

    
    public function testGetFeedUrl(): void
    {
        $this->assertIsString($this->subject->getFeedUrl());
    }

    
    public function testCheckUpdate(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->checkUpdate());
    }

    
    public function testGetFeedData(): void
    {
        $this->assertInstanceOf(SimpleXMLElement::class, $this->subject->getFeedData());
    }

    
    public function testGetFeedXml(): void
    {
        $this->assertInstanceOf(SimpleXMLElement::class, $this->subject->getFeedXml());
    }
}
