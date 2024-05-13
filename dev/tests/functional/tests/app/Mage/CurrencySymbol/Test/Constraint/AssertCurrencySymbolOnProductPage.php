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
