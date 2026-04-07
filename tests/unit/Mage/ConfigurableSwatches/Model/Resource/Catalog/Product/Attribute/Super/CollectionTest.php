<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Attribute\Super;

# use Mage;
# use Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Attribute_Super_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ConfigurableSwatches\Model\Resource\Catalog\Product\Attribute\Super\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('configurableswatches/resource_catalog_product_attribute_super_collection');
        self::markTestSkipped('');
    }
}
