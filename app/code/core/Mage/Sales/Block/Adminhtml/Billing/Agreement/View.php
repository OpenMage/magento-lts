<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */


/**
 * Adminhtml billing agreement view
 *
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize view container
     */
    public function __construct()
    {
        $this->_objectId    = 'agreement';
        $this->_controller  = 'adminhtml_billing_agreement';
        $this->_mode        = 'view';
        $this->_blockGroup  = 'sales';

        parent::__construct();

        if (!$this->_isAllowed('sales/billing_agreement/actions/manage')) {
            $this->_removeButton('delete');
        }

        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('billing_agreement_view');

        $this->_addButton('back', [
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getBackUrl()),
            'class'     => 'back',
        ], -1);

        if ($this->_getBillingAgreement()->canCancel() && $this->_isAllowed('sales/billing_agreement/actions/manage')) {
            $this->_addButton('cancel', [
                'label'     => Mage::helper('adminhtml')->__('Cancel'),
                'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs($this->_getCancelUrl()),
                'class'     => 'cancel',
            ], -1);
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Billing Agreement #%s', $this->_getBillingAgreement()->getReferenceId());
    }

    /**
     * Retrieve cancel billing agreement url
     *
     * @return string
     */
    protected function _getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', ['agreement' => $this->_getBillingAgreement()->getAgreementId()]);
    }

    /**
     * Retrieve billing agreement model
     *
     * @return Mage_Sales_Model_Billing_Agreement
     */
    protected function _getBillingAgreement()
    {
        return Mage::registry('current_billing_agreement');
    }

    /**
     * Check current user permissions for specified action
     *
     * @param  string $action
     * @return bool
     */
    protected function _isAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed($action);
    }
}
