<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogSearch\Model;

// use Mage;
// use Mage_CatalogSearch_Model_Fulltext as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogSearch\Model\FulltextTrait;

final class FulltextTest extends OpenMageTest
{
    use FulltextTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalogsearch/fulltext');
        self::markTestSkipped('');
    }
}
