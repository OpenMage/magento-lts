<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml review main block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Review_Main extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_addButtonLabel = Mage::helper('review')->__('Add New Review');
        parent::__construct();

        $this->_controller = 'review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $this->escapeHtml($customer->getName());
        }
        $productId = $this->getRequest()->getParam('productId', false);
        $productName = null;
        if ($productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productName =  $this->escapeHtml($product->getName());
        }

        if (Mage::registry('usePendingFilter') === true) {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews of Customer `%s`', $customerName);
            } else {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('All Reviews of Customer `%s`', $customerName);
            } elseif ($productName) {
                $this->_headerText = Mage::helper('review')->__('All Reviews of Product `%s`', $productName);
            } else {
                $this->_headerText = Mage::helper('review')->__('All Reviews');
            }
        }
    }
}
