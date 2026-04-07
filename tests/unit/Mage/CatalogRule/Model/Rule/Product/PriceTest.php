<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogRule\Model\Rule\Product;

# use Mage;
# use Mage_CatalogRule_Model_Rule_Product_Price as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogRule\Model\Rule\Product\PriceTrait;

final class PriceTest extends OpenMageTest
{
    use PriceTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('catalogrule/rule_product_price');
        self::markTestSkipped('');
    }
}
