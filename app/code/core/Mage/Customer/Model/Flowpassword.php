<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer flow password info Model
 *
 * @package    Mage_Customer
 *
 * @method Mage_Customer_Model_Resource_Flowpassword_Collection getCollection()
 * @method $this setEmail(string $value)
 * @method $this setIp(string $value)
 * @method $this setRequestedDate(string $value)
 */
class Mage_Customer_Model_Flowpassword extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/flowpassword');
    }

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        $this->_prepareData();
        return parent::_beforeSave();
    }

    /**
     * Prepare customer flow password data
     *
     * @return $this
     */
    protected function _prepareData()
    {
        $validatorData = Mage::getSingleton('customer/session')->getValidatorData();
        $this->setIp($validatorData[Mage_Customer_Model_Session::VALIDATOR_REMOTE_ADDR_KEY])
            ->setRequestedDate(Mage::getModel('core/date')->date());
        return $this;
    }

    /**
     * Check forgot password requests to times per 24 hours from 1 e-mail
     *
     * @param string $email
     * @return bool
     */
    public function checkCustomerForgotPasswordFlowEmail($email)
    {
        $helper = Mage::helper('customer');
        $checkForgotPasswordFlowTypes = [
            Mage_Adminhtml_Model_System_Config_Source_Customer_Forgotpassword::FORGOTPASS_FLOW_IP_EMAIL,
            Mage_Adminhtml_Model_System_Config_Source_Customer_Forgotpassword::FORGOTPASS_FLOW_EMAIL,
        ];

        if (in_array($helper->getCustomerForgotPasswordFlowSecure(), $checkForgotPasswordFlowTypes)) {
            $forgotPassword = $this->getCollection()
                ->addFieldToFilter('email', ['eq' => $email])
                ->addFieldToFilter(
                    'requested_date',
                    ['gt' => Mage::getModel('core/date')->date(null, '-1 day')],
                );

            if ($forgotPassword->getSize() > $helper->getCustomerForgotPasswordEmailTimes()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check forgot password requests to times per hour from 1 IP
     *
     * @return bool
     */
    public function checkCustomerForgotPasswordFlowIp()
    {
        $helper        = Mage::helper('customer');
        $validatorData = Mage::getSingleton('customer/session')->getValidatorData();
        $remoteAddr    = $validatorData[Mage_Customer_Model_Session::VALIDATOR_REMOTE_ADDR_KEY];
        $checkForgotPasswordFlowTypes = [
            Mage_Adminhtml_Model_System_Config_Source_Customer_Forgotpassword::FORGOTPASS_FLOW_IP_EMAIL,
            Mage_Adminhtml_Model_System_Config_Source_Customer_Forgotpassword::FORGOTPASS_FLOW_IP,
        ];

        if (in_array($helper->getCustomerForgotPasswordFlowSecure(), $checkForgotPasswordFlowTypes) && $remoteAddr) {
            $forgotPassword = $this->getCollection()
                ->addFieldToFilter('ip', ['eq' => $remoteAddr])
                ->addFieldToFilter(
                    'requested_date',
                    ['gt' => Mage::getModel('core/date')->date(null, '-1 hour')],
                );

            if ($forgotPassword->getSize() > $helper->getCustomerForgotPasswordIpTimes()) {
                return false;
            }
        }

        return true;
    }
}
