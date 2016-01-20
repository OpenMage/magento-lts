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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
