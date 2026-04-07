<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Catalog\Product;

# use Mage;
use Mage_Adminhtml_Helper_Catalog_Product_Composite as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper\Catalog\Product\CompositeTrait;

final class CompositeTest extends OpenMageTest
{
    use CompositeTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::helper('adminhtml/catalog_product_composite');
        self::markTestSkipped('');
    }
}
