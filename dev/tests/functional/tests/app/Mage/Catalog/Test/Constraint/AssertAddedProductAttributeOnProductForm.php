<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
