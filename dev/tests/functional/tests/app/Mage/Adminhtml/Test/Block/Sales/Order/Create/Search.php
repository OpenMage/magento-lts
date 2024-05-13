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
