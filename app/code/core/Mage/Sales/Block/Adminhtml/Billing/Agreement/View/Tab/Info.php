<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml billing agreement info tab
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setCreatedAt(string $formatDate)
 * @method $this setCustomerEmail(string $value)
 * @method $this setCustomerUrl(string $value)
 * @method $this setReferenceId(string $value)
 * @method $this setStatus(string $value)
 * @method $this setUpdatedAt(string $value)
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Info extends Mage_Adminhtml_Block_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set custom template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/billing/agreement/view/tab/info.phtml');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('General Information');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('General Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
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
     * Set data to block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $agreement = $this->_getBillingAgreement();
        $this->setReferenceId($agreement->getReferenceId());
        $customer = Mage::getModel('customer/customer')->load($agreement->getCustomerId());
        $this->setCustomerUrl(
            $this->getUrl('*/customer/edit', ['id' => $customer->getId()])
        );
        $this->setCustomerEmail($customer->getEmail());
        $this->setStatus($agreement->getStatusLabel());
        $this->setCreatedAt($this->formatDate($agreement->getCreatedAt(), 'short', true));
        $this->setUpdatedAt(
            ($agreement->getUpdatedAt())
                ? $this->formatDate($agreement->getUpdatedAt(), 'short', true) : $this->__('N/A')
        );

        return parent::_toHtml();
    }
}
