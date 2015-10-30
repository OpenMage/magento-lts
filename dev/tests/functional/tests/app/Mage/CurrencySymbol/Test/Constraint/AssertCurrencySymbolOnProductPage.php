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

namespace Mage\CurrencySymbol\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Catalog\Test\Fixture\CatalogProductSimple;
use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Mage\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Mage\Adminhtml\Test\Page\Adminhtml\Cache;

/**
 * Check that after applying changes, currency symbol changed on Product Details Page.
 */
class AssertCurrencySymbolOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after applying changes, currency symbol changed on Product Details Page.
     *
     * @param CatalogProductSimple $product
     * @param Browser $browser
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CurrencySymbolEntity $currencySymbol
     * @param Cache $adminCache
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        Browser $browser,
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CurrencySymbolEntity $currencySymbol,
        Cache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getPageActions()->flushCacheStorage();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $cmsIndex->open();
        $cmsIndex->getCurrencyBlock()->switchCurrency($currencySymbol);
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $price = $catalogProductView->getViewBlock()->getPriceBlock()->getPrice();
        preg_match('`(.*?)\d`', $price, $matches);

        $symbolOnPage = isset($matches[1]) ? $matches[1] : null;
        \PHPUnit_Framework_Assert::assertEquals(
            $currencySymbol->getCustomCurrencySymbol(),
            $symbolOnPage,
            'Wrong Currency Symbol is displayed on Product page.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Currency Symbol has been changed on Product Details page.";
    }
}
