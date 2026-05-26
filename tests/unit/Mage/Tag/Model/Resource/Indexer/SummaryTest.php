<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tag\Model\Resource\Indexer;

// use Mage;
// use Mage_Tag_Model_Resource_Indexer_Summary as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tag\Model\Resource\Indexer\SummaryTrait;

final class SummaryTest extends OpenMageTest
{
    use SummaryTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tag/resource_indexer_summary');
        self::markTestSkipped('');
    }
}
