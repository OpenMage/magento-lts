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
use OpenMage\Tests\Unit\OpenMageTest;

final class AggregationTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('log/aggregation');
    }

    /**
     * @group Model
     * @doesNotPerformAssertions
     */
    public function testRun(): void
    {
        self::$subject->run();
    }
}
