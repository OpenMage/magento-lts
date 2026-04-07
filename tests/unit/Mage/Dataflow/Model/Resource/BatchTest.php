<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Resource;

// use Mage;
// use Mage_Dataflow_Model_Resource_Batch as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Resource\BatchTrait;

final class BatchTest extends OpenMageTest
{
    use BatchTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('dataflow/resource_batch');
        self::markTestSkipped('');
    }
}
