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

use Mage\Catalog\Test\Fixture\CatalogAttributeSet;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Checking data from Product Template form with data fixture.
 */
class AssertProductTemplateForm extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Assert that after save a product template on edit product set page displays:
     * 1. Correct product template name in Attribute set name field passed from fixture
     * 2. Created Product Attribute (if was added)
     *
     * @param CatalogProductSetIndex $productSet
     * @param CatalogProductSetEdit $productSetEdit
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductAttribute $productAttribute [optional]
     * @return void
     */
    public function processAssert(
        CatalogProductSetIndex $productSet,
        CatalogProductSetEdit $productSetEdit,
        CatalogAttributeSet $attributeSet,
        CatalogProductAttribute $productAttribute = null
    ) {
        $filterAttribute = [
            'set_name' => $attributeSet->getAttributeSetName(),
        ];
        $productSet->open();
        $productSet->getGrid()->searchAndOpen($filterAttribute);
        \PHPUnit_Framework_Assert::assertEquals(
            $filterAttribute['set_name'],
            $productSetEdit->getAttributeSetEditBlock()->getAttributeSetName()
        );
        if ($productAttribute !== null) {
            $attributeLabel = $productAttribute->getFrontendLabel();
            \PHPUnit_Framework_Assert::assertTrue(
                $productSetEdit->getAttributeSetEditBlock()->checkProductAttribute($attributeLabel),
                "Product Attribute '$attributeLabel' is absent on Product Template Groups."
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Data from the Product Template form matched with fixture.';
    }
}
