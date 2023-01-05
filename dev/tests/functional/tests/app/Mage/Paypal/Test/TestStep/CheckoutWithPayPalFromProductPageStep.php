<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\TestStep;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Proceed to pay pal checkout from product page.
 */
class CheckoutWithPayPalFromProductPageStep implements TestStepInterface
{
    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Products' fixtures.
     *
     * @var array
     */
    protected $products;

    /**
     * Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param array $products
     */
    public function __construct(CatalogProductView $catalogProductView, Browser $browser, array $products)
    {
        $this->catalogProductView = $catalogProductView;
        $this->products = $products;
        $this->browser = $browser;
    }

    /**
     * Pay Pal checkout from product page.
     *
     * @return void
     */
    public function run()
    {
        $this->browser->open($_ENV['app_frontend_url'] . $this->products[0]->getUrlKey() . '.html');
        $this->catalogProductView->getPaypalViewBlock()->paypalCheckout($this->products[0]);
    }
}
