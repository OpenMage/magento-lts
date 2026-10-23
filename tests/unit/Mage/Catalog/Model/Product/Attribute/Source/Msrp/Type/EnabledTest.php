<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Attribute\Source\Msrp\Type;

// use Mage;
// use Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Attribute\Source\Msrp\Type\EnabledTrait;

final class EnabledTest extends OpenMageTest
{
    use EnabledTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/product_attribute_source_msrp_type_enabled');
        self::markTestSkipped('');
    }
}
