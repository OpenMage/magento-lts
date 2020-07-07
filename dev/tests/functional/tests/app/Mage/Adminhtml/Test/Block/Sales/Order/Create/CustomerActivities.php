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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Mage\Adminhtml\Test\Block\Template;
use Mage\Adminhtml\Test\Block\Sales\Order\Create\CustomerActivities\Sidebar\ShoppingCartItems;
use Magento\Mtf\Block\Block;
use Magento\Mtf\Client\Locator;

/**
 * Customer's Activities block
 */
class CustomerActivities extends Block
{
    /**
     * 'Update Changes' button.
     *
     * @var string
     */
    protected $updateChanges = '[onclick^="order.sidebarApplyChanges"]';

    /**
     * Shopping cart sidebar selector.
     *
     * @var string
     */
    protected $shoppingCartSidebar = '#order-sidebar_cart';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Get shopping Cart items block.
     *
     * @return ShoppingCartItems
     */
    public function getShoppingCartItemsBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Sales\Order\Create\CustomerActivities\Sidebar\ShoppingCartItems',
            ['element' => $this->_rootElement->find($this->shoppingCartSidebar)]
        );
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    public function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Click 'Update Changes' button.
     *
     * @return void
     */
    public function updateChanges()
    {
        $this->_rootElement->find($this->updateChanges)->click();
        $this->getTemplateBlock()->waitLoader();
    }
}
