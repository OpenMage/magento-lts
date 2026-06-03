<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales abstract model
 * Provide date processing functionality
 *
 * @method Mage_Sales_Model_Resource_Order_Abstract _getResource()
 * @method string                                   getBackUrl()
 * @method Mage_Customer_Model_Address_Abstract     getBillingAddress()
 * @method bool                                     getForceUpdateGridRecords()
 * @method Mage_Sales_Model_Resource_Order_Abstract getResource()
 * @method Mage_Customer_Model_Address_Abstract     getShippingAddress()
 * @method int                                      getStoreId()
 * @method $this                                    setBillingAddress(Mage_Customer_Model_Address_Abstract $address)
 * @method $this                                    setShippingAddress(Mage_Customer_Model_Address_Abstract $address)
 * @method $this                                    setStoreId(int $value)
 * @method $this                                    setTransactionId(int $value)
 */
abstract class Mage_Sales_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Get object store identifier
     *
     * @return int|Mage_Core_Model_Store|string
     */
    abstract public function getStore();

    /**
     * Processing object after save data
     * Updates relevant grid table records.
     *
     * @return $this
     */
    #[Override]
    public function afterCommitCallback()
    {
        if (!$this->getForceUpdateGridRecords()) {
            $this->_getResource()->updateGridRecords($this->getId());
        }

        return parent::afterCommitCallback();
    }

    /**
     * Get object created at date affected current active store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtDate()
    {
        return Mage::app()->getLocale()->date(
            Varien_Date::toTimestamp($this->getCreatedAt()),
            null,
            null,
            true,
        );
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @return Zend_Date
     */
    public function getCreatedAtStoreDate()
    {
        return Mage::app()->getLocale()->storeDate(
            $this->getStore(),
            Varien_Date::toTimestamp($this->getCreatedAt()),
            true,
        );
    }

    protected function getMailer(): Mage_Core_Model_Email_Template_Mailer
    {
        /**
         * @var Mage_Core_Model_Email_Template_Mailer $mailer
         */
        $mailer = Mage::getModel('core/email_template_mailer');
        return $mailer;
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getPaymentBlockHtml(Mage_Sales_Model_Order $order): ?string
    {
        $storeId = $order->getStore()->getId();
        $payment = $order->getPayment();
        if (!is_null($storeId) && $payment instanceof Mage_Payment_Model_Info) {
            // Start store emulation process
            if ($storeId !== Mage::app()->getStore()->getId()) {
                $appEmulation = Mage::getSingleton('core/app_emulation');
                $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
            }
            try {
                // Retrieve specified view block from appropriate design package (depends on emulated store)
                $paymentBlock = Mage::helper('payment')->getInfoBlock($payment)
                    ->setIsSecureMode(true);
                $paymentBlock->getMethod()->setStore($storeId);
                return $paymentBlock->toHtml();
            } finally {
                // Stop store emulation process
                if (isset($appEmulation, $initialEnvironmentInfo)) {
                    $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
                }
            }
        }
        return null;
    }

    /**
     * @param  string             $configPath
     * @return false|list<string>
     */
    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (is_string($data) && $data !== '') {
            return explode(',', $data);
        }

        return false;
    }
}
