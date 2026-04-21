<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Batch;

// use Mage;
// use Mage_Dataflow_Model_Batch_Io as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Batch\IoTrait;

final class IoTest extends OpenMageTest
{
    use IoTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('dataflow/batch_io');
        self::markTestSkipped('');
    }
}
