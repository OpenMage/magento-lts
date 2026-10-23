<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogIndex\Model\Catalog\Index;

// use Mage;
// use Mage_CatalogIndex_Model_Catalog_Index_Flag as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogIndex\Model\Catalog\Index\FlagTrait;

final class FlagTest extends OpenMageTest
{
    use FlagTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalogindex/catalog_index_flag');
        self::markTestSkipped('');
    }
}
