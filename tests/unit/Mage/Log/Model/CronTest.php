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

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Cron;
use PHPUnit\Framework\TestCase;

class CronTest extends TestCase
{
    public Mage_Log_Model_Cron $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('log/cron');
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     */
    public function testLogClean(): void
    {
        $this->assertInstanceOf(Mage_Log_Model_Cron::class, $this->subject->logClean());
    }
}
