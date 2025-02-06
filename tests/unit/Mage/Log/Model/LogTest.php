<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
