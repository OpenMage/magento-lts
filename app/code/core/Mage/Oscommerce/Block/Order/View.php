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
 * @category    Mage
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce order view block
 * 
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Order_View extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('oscommerce/order/view.phtml');
    }

    protected function _prepareLayout()
    {
        $orderInfo = $this->getOrder();
        $order = $orderInfo['order'];
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Order # %s', $order['orders_id']));
        }
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_oscommerce_order');
    }

    public function getBackUrl()
    {
        return Mage::getUrl('sales/order/history');
    }

}
