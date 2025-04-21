<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Customer as Subject;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('log/customer');
    }

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     */
    public function testGetLoginAtTimestamp(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getLoginAt'])
            ->getMock();

        $this->assertNull($mock->getLoginAtTimestamp());

        $mock->method('getLoginAt')->willReturn(true);
        $this->assertIsInt($mock->getLoginAtTimestamp());
    }
}
