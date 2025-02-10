<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Log
 * @group Mage_Log_Model
 * @group runInSeparateProcess
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Visitor as Subject;
use PHPUnit\Framework\TestCase;

class VisitorTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('log/visitor');
    }


    public function testInitServerData(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->initServerData());
    }


    public function testGetOnlineMinutesInterval(): void
    {
        $this->assertIsInt($this->subject->getOnlineMinutesInterval());
    }


    public function testGetUrl(): void
    {
        $this->assertIsString($this->subject->getUrl());
    }


    public function testGetFirstVisitAt(): void
    {
        $this->assertIsString($this->subject->getFirstVisitAt());
    }


    public function testGetLastVisitAt(): void
    {
        $this->assertIsString($this->subject->getLastVisitAt());
    }
}
