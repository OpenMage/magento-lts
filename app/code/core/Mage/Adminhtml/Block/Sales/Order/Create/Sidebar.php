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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create sidebar
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar');
        $this->setTemplate('sales/order/create/sidebar.phtml');
    }

    protected function _prepareLayout()
    {
        if ($this->getCustomerId()) {
            $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label' => Mage::helper('sales')->__('Update Changes'),
                'onclick' => 'order.sidebarApplyChanges()',
                'before_html' => '<div class="sub-btn-set">',
                'after_html' => '</div>'
            ));
            $this->setChild('top_button', $button);
        }
        $this->setChild('cart', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_cart'));
        $this->setChild('wishlist', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_wishlist'));
        $this->setChild('reorder', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_reorder'));
        $this->setChild('viewed', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_viewed'));
        $this->setChild('compared', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_compared'));
        $this->setChild('pcompared', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_pcompared'));
        $this->setChild('pviewed', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_pviewed'));
        if ($this->getCustomerId()) {
            $button = clone $button;
            $button->unsId();
            $this->setChild('bottom_button', $button);
        }
        return parent::_prepareLayout();
    }

    public function canDisplay($child)
    {
        if (method_exists($child, 'canDisplay')) {
            return $child->canDisplay();
        }
        return true;
    }
}
