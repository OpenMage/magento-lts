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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Product\CatalogProductCompare;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Mtf\Fixture\InjectableFixture;

/**
 * Check whether there is an opportunity to compare products using given attribute.
 */
class AssertProductAttributeIsComparable extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Check whether there is an opportunity to compare products using given attribute.
     *
     * @param InjectableFixture $product
     * @param CatalogProductAttribute $attribute
     * @param BrowserInterface $browser
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductCompare $catalogProductCompare
     * @return void
     */
    public function processAssert(
        InjectableFixture $product,
        CatalogProductAttribute $attribute,
        BrowserInterface $browser,
        CatalogProductView $catalogProductView,
        CatalogProductCompare $catalogProductCompare
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCompare();
        $catalogProductCompare->open();
        $label = $attribute->hasData('manage_frontend_label')
            ? $attribute->getManageFrontendLabel()
            : $attribute->getFrontendLabel();

        \PHPUnit_Framework_Assert::assertTrue(
            $catalogProductCompare->getCompareProductsBlock()->isAttributeVisible($label),
            'Attribute is absent on product compare page.'
        );
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Attribute is present on product compare page.';
    }
}
