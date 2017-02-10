<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;

/**
 * Assert that created attribute is displayed on product form.
 */
class AssertAddedProductAttributeOnProductForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created attribute is displayed on product form.
     *
     * @param InjectableFixture $product
     * @param CatalogProduct $productGrid
     * @param CatalogProductEdit $productEdit
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProduct $productGrid,
        CatalogProductEdit $productEdit,
        CatalogProductAttribute $attribute
    ) {
        $productGrid->open();
        $productGrid->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);

        \PHPUnit_Framework_Assert::assertTrue(
            $productEdit->getProductForm()->checkAttributeLabel($attribute->getData()),
            "Product Attribute is absent on Product form."
        );
    }

    /**
     * Returns string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Attribute is present on Product form.';
    }
}
