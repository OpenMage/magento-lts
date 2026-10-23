<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Newsletter\Model\Resource;

// use Mage;
// use Mage_Newsletter_Model_Resource_Queue as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Newsletter\Model\Resource\QueueTrait;

final class QueueTest extends OpenMageTest
{
    use QueueTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('newsletter/resource_queue');
        self::markTestSkipped('');
    }
}
