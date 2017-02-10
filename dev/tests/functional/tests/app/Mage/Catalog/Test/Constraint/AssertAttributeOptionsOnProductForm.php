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

use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Assert that configurable options available on configurable product Edit form on backend.
 */
class AssertAttributeOptionsOnProductForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that configurable options available on configurable product Edit form on backend.
     *
     * @param CatalogProduct $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductAttribute $attribute
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CatalogProduct $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductAttribute $attribute,
        InjectableFixture $product
    ) {
        $options = $attribute->getOptions();
        $attributeOptionsFromFixture = [];
        foreach ($options as $option) {
            $attributeOptionsFromFixture[] = $option['admin'];
        }
        $catalogProductIndex->open()->getProductGrid()->searchAndOpen(['sku' => $product->getSku()]);
        $productForm = $catalogProductEdit->getProductForm();
        $attributeOptions = $productForm->getAttributeElement($attribute)->getValues();

        \PHPUnit_Framework_Assert::assertEquals($attributeOptionsFromFixture, $attributeOptions);
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return "Configurable options are available on configurable product Edit form.";
    }
}
