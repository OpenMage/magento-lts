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

namespace Mage\Wishlist\Test\TestStep;

use Mage\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Adding created products to the wish list.
 */
class AddProductsToWishlistStep implements TestStepInterface
{
    /**
     * Array with products.
     *
     * @var array
     */
    protected $products;

    /**
     * Frontend product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Interface Browser.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Configure flag.
     *
     * @var bool
     */
    protected $configure;

    /**
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param array $products
     * @param bool $configure [optional]
     */
    public function __construct(
        CatalogProductView $catalogProductView,
        Browser $browser,
        array $products,
        $configure = false
    ) {
        $this->products = $products;
        $this->catalogProductView = $catalogProductView;
        $this->browser = $browser;
        $this->configure = $configure;
    }

    /**
     * Add products to the wish list.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $viewBlock = $this->catalogProductView->getViewBlock();
            $this->configure ? $viewBlock->addToWishlist($product) : $viewBlock->clickAddToWishlist();
        }
    }
}
