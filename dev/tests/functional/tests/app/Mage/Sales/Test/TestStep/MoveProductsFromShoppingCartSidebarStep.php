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
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Move products from shopping cart sidebar on create order page.
 */
class MoveProductsFromShoppingCartSidebarStep implements TestStepInterface
{
    /**
     * Sales order create index page.
     *
     * @var SalesOrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Array of products fixtures.
     *
     * @var array
     */
    protected $products;

    /**
     * @constructor
     * @param SalesOrderCreateIndex $orderCreateIndex
     * @param array $products
     */
    public function __construct(SalesOrderCreateIndex $orderCreateIndex, array $products)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->products = $products;
    }

    /**
     * Move products from shopping cart sidebar on create order page.
     *
     * @return void
     */
    public function run()
    {
        $this->orderCreateIndex->getCustomerActivitiesBlock()->getShoppingCartItemsBlock()->addToOrder($this->products);
        $this->orderCreateIndex->getCustomerActivitiesBlock()->updateChanges();
    }
}
