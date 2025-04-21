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

    /**
     * @group Mage_Log
     * @group Mage_Log_Model
     * @doesNotPerformAssertions
     */
    public function testRun(): void
    {
        $this->subject->run();
    }
}
