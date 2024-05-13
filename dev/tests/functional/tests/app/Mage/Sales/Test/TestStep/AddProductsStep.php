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

namespace Mage\Sales\Test\TestStep;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreateIndex;
use Magento\Mtf\Fixture\InjectableFixture;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Add products to order during creating backend order.
 */
class AddProductsStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $salesOrderCreateIndex;

    /**
     * Array products.
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $salesOrderCreateIndex
     * @param InjectableFixture[] $products
     */
    public function __construct(SalesOrderCreateIndex $salesOrderCreateIndex, array $products)
    {
        $this->salesOrderCreateIndex = $salesOrderCreateIndex;
        $this->products = $products;
    }

    /**
     * Add products to order during creating backend order.
     *
     * @return void
     */
    public function run()
    {
        $createBlock = $this->salesOrderCreateIndex->getCreateBlock();
        $createBlock->getItemsBlock()->clickAddProducts();
        $productsGrid = $createBlock->getSearchBlock()->getSearchGrid();
        foreach ($this->products as $product) {
            $productsGrid->searchAndSelect(['sku' => $product->getSku()]);
        }
        $createBlock->getSearchBlock()->addSelectedProductsToOrder();
        $createBlock->getTemplateBlock()->waitLoader();
    }
}
