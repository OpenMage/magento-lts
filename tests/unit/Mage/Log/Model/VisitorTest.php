<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testInitServerData(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->initServerData());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetOnlineMinutesInterval(): void
    {
        $this->assertIsInt($this->subject->getOnlineMinutesInterval());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetUrl(): void
    {
        $this->assertIsString($this->subject->getUrl());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFirstVisitAt(): void
    {
        $this->assertIsString($this->subject->getFirstVisitAt());
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetLastVisitAt(): void
    {
        $this->assertIsString($this->subject->getLastVisitAt());
    }
}
