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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml creditmemo create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_creditmemo';
        $this->_mode = 'create';

        parent::__construct();

        $this->_removeButton('delete');
        $this->_removeButton('save');

        /*$this->_addButton('submit_creditmemo', array(
            'label'     => Mage::helper('sales')->__('Submit Credit Memo'),
            'class'     => 'save submit-button',
            'onclick'   => '$(\'edit_form\').submit()',
            )
        );*/

    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getInvoice()) {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Invoice #%s',
                $this->escapeHtml($this->getCreditmemo()->getInvoice()->getIncrementId())
            );
        } else {
            $header = Mage::helper('sales')->__(
                'New Credit Memo for Order #%s',
                $this->escapeHtml($this->getCreditmemo()->getOrder()->getRealOrderId())
            );
        }

        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$this->getCreditmemo()->getOrderId()));
    }
}
