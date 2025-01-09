<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Visitor;
use PHPUnit\Framework\TestCase;

class VisitorTest extends TestCase
{
    public Mage_Log_Model_Visitor $subject;

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
        $this->assertInstanceOf(Mage_Log_Model_Visitor::class, $this->subject->initServerData());
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
