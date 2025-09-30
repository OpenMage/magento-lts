<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payment transaction model
 * Tracks transaction history
 *
 * @package    Mage_Paypal
 *
 * @method Mage_Paypal_Model_Resource_Payment_Transaction _getResource()
 * @method Mage_Paypal_Model_Resource_Payment_Transaction getResource()
 * @method string getTxnId()
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 */
class Mage_Paypal_Model_Payment_Transaction extends Mage_Core_Model_Abstract
{
    /**
     * Whether to throw exceptions on different operations
     *
     * @var bool
     */
    protected $_isFailsafe = false;

    /**
     * @see Mage_Core_Model_Absctract::$_eventPrefix
     * @var string
     */
    protected $_eventPrefix = 'paypal_payment_transaction';

    /**
     * @see Mage_Core_Model_Absctract::$_eventObject
     * @var string
     */
    protected $_eventObject = 'paypal_payment_transaction';

    /**
     * Order website id
     *
     * @var int
     */
    protected $_orderWebsiteId = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('paypal/payment_transaction');
        parent::_construct();
    }

    /**
     * Transaction ID setter
     * @param string $txnId
     * @return $this
     */
    public function setTxnId($txnId)
    {
        $this->_verifyTxnId($txnId);
        return $this->setData('txn_id', $txnId);
    }

    /**
     * Check object before loading by by specified transaction ID
     * @param string $txnId
     * @return $this
     */
    protected function _beforeLoadByTxnId($txnId)
    {
        Mage::dispatchEvent(
            $this->_eventPrefix . '_load_by_txn_id_before',
            $this->_getEventData() + ['txn_id' => $txnId],
        );
        return $this;
    }

    /**
     * Load self by specified transaction ID. Requires the valid payment object to be set
     * @param string $txnId
     * @return $this
     */
    public function loadByTxnId($txnId)
    {
        $this->_beforeLoadByTxnId($txnId);
        $this->getResource()->loadObjectByTxnId(
            $this,
            $txnId,
        );
        $this->_afterLoadByTxnId();
        return $this;
    }

    /**
     * Check object after loading by by specified transaction ID
     *
     * @return $this
     */
    protected function _afterLoadByTxnId()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_load_by_txn_id_after', $this->_getEventData());
        return $this;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * Doesn't allow to set arrays
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function setAdditionalInformation($key, $value)
    {
        if (is_object($value)) {
            Mage::throwException(Mage::helper('paypal')->__('Payment transactions disallow storing objects.'));
        }
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = [];
        }
        $info[$key] = $value;
        return $this->setData('additional_information', $info);
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     * @param string $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = [];
        }
        if ($key) {
            return $info[$key] ?? null;
        }
        return $info;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     * @param string $key
     * @return $this
     */
    public function unsAdditionalInformation($key = null)
    {
        if ($key) {
            $info = $this->_getData('additional_information');
            if (is_array($info)) {
                unset($info[$key]);
            }
        } else {
            $info = [];
        }
        return $this->setData('additional_information', $info);
    }

    /**
     * Setter/Getter whether transaction is supposed to prevent exceptions on saving
     *
     * @param bool|null $setFailsafe
     * @return bool|$this
     */
    public function isFailsafe($setFailsafe = null)
    {
        if ($setFailsafe === null) {
            return $this->_isFailsafe;
        }
        $this->_isFailsafe = (bool) $setFailsafe;
        return $this;
    }

    /**
     * Verify data required for saving
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setCreatedAt(Mage::getModel('core/date')->gmtDate());
        }
        return parent::_beforeSave();
    }

    /**
     * Check whether specified transaction ID is valid
     * @param string $txnId
     * @throws Mage_Core_Exception
     */
    protected function _verifyTxnId($txnId)
    {
        if ($txnId !== null && strlen($txnId) == 0) {
            Mage::throwException(Mage::helper('paypal')->__('Transaction ID must not be empty.'));
        }
    }

    /**
     * Make sure this object is a valid transaction
     * TODO for more restriction we can check for data consistency
     * @throws Mage_Core_Exception
     */
    protected function _verifyThisTransactionExists()
    {
        if (!$this->getId()) {
            Mage::throwException(Mage::helper('paypal')->__('This operation requires an existing transaction object.'));
        }
    }
}
