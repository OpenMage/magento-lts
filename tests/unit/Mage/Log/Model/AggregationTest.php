<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Log
 * @group Mage_Log_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Aggregation as Subject;
use PHPUnit\Framework\TestCase;

class AggregationTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('log/aggregation');
    }

    
    public function testRun(): void
    {
        $this->subject->run();
    }
}
