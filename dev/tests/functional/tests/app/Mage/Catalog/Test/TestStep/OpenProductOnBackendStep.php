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

namespace Mage\Catalog\Test\TestStep;

use Mage\Catalog\Test\Page\Adminhtml\CatalogProduct;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Open product on backend.
 */
class OpenProductOnBackendStep implements TestStepInterface
{
    /**
     * Product fixture.
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Catalog product index page.
     *
     * @var CatalogProduct
     */
    protected $catalogProductIndex;

    /**
     * @constructor
     * @param InjectableFixture $product
     * @param CatalogProduct $catalogProductIndex
     */
    public function __construct(InjectableFixture $product, CatalogProduct $catalogProductIndex)
    {
        $this->product = $product;
        $this->catalogProductIndex = $catalogProductIndex;
    }

    /**
     * Open products on backend.
     *
     * @return void
     */
    public function run()
    {
        $this->catalogProductIndex->open();
        $this->catalogProductIndex->getProductGrid()->searchAndOpen(['sku' => $this->product->getSku()]);
    }
}
