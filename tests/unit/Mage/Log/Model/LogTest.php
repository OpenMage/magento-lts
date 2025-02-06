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
use Mage_Log_Model_Log as Subject;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('log/log');
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     */
    public function testClean(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->clean());
    }
}
