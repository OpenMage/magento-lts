<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Type\Configurable\Product;

// use Mage;
// use Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable_Product_Collection as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Type\Configurable\Product\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('configurableswatches/resource_catalog_product_type_configurable_product_collection');
        self::markTestSkipped('');
    }
}
