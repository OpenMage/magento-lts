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
