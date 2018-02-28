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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\CatalogSearch\Test\Page\CatalogsearchResult;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Check out if the attribute in the navigation bar on the search results page in Layered navigation.
 */
class AssertProductAttributeIsFilterableInSearch extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Check out if the attribute in the navigation bar on the search results page in Layered navigation.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogsearchResult $catalogsearchResult
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogsearchResult $catalogsearchResult,
        InjectableFixture $product,
        CatalogProductAttribute $attribute
    ) {
        $cmsIndex->open()->getSearchBlock()->search($product->getName());
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(strtoupper($label), $catalogsearchResult->getLayeredNavigationBlock()->getFilters()),
            'Attribute is absent in layered navigation on search page.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present in layered navigation on search page.';
    }
}
