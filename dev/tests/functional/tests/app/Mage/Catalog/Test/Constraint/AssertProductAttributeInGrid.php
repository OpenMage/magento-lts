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

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductAttributeIndex;

/**
 * Assert that created product attribute exist in grid.
 */
class AssertProductAttributeInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created product attribute exist in grid.
     *
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductAttributeIndex $attributeIndexPage
     * @return void
     */
    public function processAssert(CatalogProductAttribute $attribute, CatalogProductAttributeIndex $attributeIndexPage)
    {
        $attributeIndexPage->open();
        $code = $attribute->getAttributeCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $attributeIndexPage->getGrid()->isRowVisible(['attribute_code' => $code]),
            'Attribute with attribute code "' . $code . '" is absent in attribute grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product attribute is present in attribute grid.';
    }
}
