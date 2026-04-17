<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer group edit block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Group_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'customer_group';

        if (!Mage::registry('current_group')->getId() || Mage::registry('current_group')->usesAsDefault()) {
            $this->_removeButton('delete');
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDeleteUrl()
    {
        if (!Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            return $this->getUrl('*/*/delete', [
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'form_key' => Mage::getSingleton('core/session')->getFormKey(),
            ]);
        }

        return parent::getDeleteUrl();
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (!is_null(Mage::registry('current_group')->getId())) {
            return Mage::helper('customer')->__('Edit Customer Group "%s"', $this->escapeHtml(Mage::registry('current_group')->getCustomerGroupCode()));
        }

        return Mage::helper('customer')->__('New Customer Group');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
