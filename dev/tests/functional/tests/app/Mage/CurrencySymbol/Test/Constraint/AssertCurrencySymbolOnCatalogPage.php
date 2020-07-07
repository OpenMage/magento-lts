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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\CurrencySymbol\Test\Constraint;

use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Category\CatalogCategoryView;
use Mage\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;

/**
 * Check that after applying changes, currency symbol changed on Catalog page.
 */
class AssertCurrencySymbolOnCatalogPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after applying changes, currency symbol changed on Catalog page.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $product
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $product,
        CurrencySymbolEntity $currencySymbol
    ) {
        $categoryName = $product->getCategoryIds()[0];
        $cmsIndex->open();
        $cmsIndex->getCurrencyBlock()->switchCurrency($currencySymbol);
        $cmsIndex->getTopmenu()->selectCategory($categoryName);
        $price = $catalogCategoryView->getListProductBlock()->getPrice($product->getId());
        preg_match('`(.*?)\d`', $price, $matches);

        $symbolOnPage = isset($matches[1]) ? $matches[1] : null;
        \PHPUnit_Framework_Assert::assertEquals(
            $currencySymbol->getCustomCurrencySymbol(),
            $symbolOnPage,
            'Wrong Currency Symbol is displayed on Category page.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Currency Symbol has been changed on Catalog page.";
    }
}
