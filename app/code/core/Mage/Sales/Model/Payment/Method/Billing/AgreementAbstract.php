<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Sales Billing Agreement Payment Method Abstract model
 *
 * @package    Mage_Sales
 */
abstract class Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Transport billing agreement id
     *
     */
    public const TRANSPORT_BILLING_AGREEMENT_ID = 'ba_agreement_id';
    public const PAYMENT_INFO_REFERENCE_ID      = 'ba_reference_id';

    protected $_infoBlockType = 'sales/payment_info_billing_agreement';
    protected $_formBlockType = 'sales/payment_form_billing_agreement';

    /**
     * Is method instance available
     *
     * @var null|bool
     */
    protected $_isAvailable = null;

    /**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (is_null($this->_isAvailable)) {
            if (is_object($quote) && $quote->getCustomer()) {
                $availableBA = Mage::getModel('sales/billing_agreement')->getAvailableCustomerBillingAgreements(
                    $quote->getCustomer()->getId(),
                );
                $isAvailableBA = count($availableBA) > 0;
                $this->_canUseForMultishipping = $this->_canUseCheckout = $this->_canUseInternal = $isAvailableBA;
            }
            $this->_isAvailable = parent::isAvailable($quote) && $this->_isAvailable($quote);
            $this->_canUseCheckout = ($this->_isAvailable && $this->_canUseCheckout);
            $this->_canUseForMultishipping = ($this->_isAvailable && $this->_canUseForMultishipping);
            $this->_canUseInternal = ($this->_isAvailable && $this->_canUseInternal);
        }
        return $this->_isAvailable;
    }

    /**
     * Assign data to info model instance
     *
     * @param mixed $data
     * @return Mage_Payment_Model_Method_Abstract
     * @throws Mage_Core_Exception
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);

        $key = self::TRANSPORT_BILLING_AGREEMENT_ID;
        $id = false;
        if (is_array($data) && isset($data[$key])) {
            $id = $data[$key];
        } elseif ($data instanceof Varien_Object && $data->getData($key)) {
            $id = $data->getData($key);
        }
        if ($id) {
            $info = $this->getInfoInstance();
            $ba = Mage::getModel('sales/billing_agreement')->load($id);
            if ($ba->getId() && $ba->getCustomerId() == $info->getQuote()->getCustomer()->getId()) {
                $info->setAdditionalInformation($key, $id)
                    ->setAdditionalInformation(self::PAYMENT_INFO_REFERENCE_ID, $ba->getReferenceId());
            }
        }
        return $result;
    }

    /**
     *
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    abstract protected function _isAvailable($quote);
}
