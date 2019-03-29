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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Magento\Mtf\Block\Block;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\Search\Grid;

/**
 * Adminhtml sales order create search items block.
 */
class Search extends Block
{
    /**
     * Search products grid selector.
     *
     * @var string
     */
    protected $gridSelector = '#sales_order_create_search_grid';

    /**
     * 'Add Selected Product(s) to Order' button.
     *
     * @var string
     */
    protected $addSelectedProducts = 'button[onclick="order.productGridAddSelected()"]';

    /**
     * Click "Add Selected Product(s) to Order" button.
     *
     * @return void
     */
    public function addSelectedProductsToOrder()
    {
        $this->_rootElement->find($this->addSelectedProducts)->click();
    }

    /**
     * Get search products grid.
     *
     * @return Grid
     */
    public function getSearchGrid()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\Search\Grid',
            ['element' => $this->_rootElement->find($this->gridSelector)]
        );
    }
}
