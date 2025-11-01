<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * Application model
 *
 * @package    Mage_Oauth
 *
 * @method Mage_Oauth_Model_Resource_Consumer _getResource()
 * @method Mage_Oauth_Model_Resource_Consumer getResource()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getResourceCollection()
 * @method string getName()
 * @method $this setName() setName(string $name)
 * @method string getKey()
 * @method $this setKey() setKey(string $key)
 * @method string getSecret()
 * @method $this setSecret() setSecret(string $secret)
 * @method string getCallbackUrl()
 * @method $this setCallbackUrl() setCallbackUrl(string $url)
 * @method string getCreatedAt()
 * @method $this setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt() setUpdatedAt(string $date)
 * @method string getRejectedCallbackUrl()
 * @method $this setRejectedCallbackUrl() setRejectedCallbackUrl(string $rejectedCallbackUrl)
 */
class Mage_Oauth_Model_Consumer extends Mage_Core_Model_Abstract
{
    /**
     * Key hash length
     */
    public const KEY_LENGTH = 32;

    /**
     * Secret hash length
     */
    public const SECRET_LENGTH = 32;

    protected function _construct()
    {
        $this->_init('oauth/consumer');
    }

    /**
     * BeforeSave actions
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setUpdatedAt(time());
        }

        $this->setCallbackUrl(trim($this->getCallbackUrl()));
        $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));
        $this->validate();
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return bool
     * @throws Mage_Core_Exception   Throw exception on fail validation
     */
    public function validate()
    {
        $validator = $this->getValidationHelper();

        $violations = $validator->validateLength(value: $this->getKey(), exactly: self::KEY_LENGTH);
        if ($violations->count() > 0) {
            Mage::throwException($violations->get(0)->getMessage());
        }

        $violations = $validator->validateLength(value: $this->getSecret(), exactly: self::SECRET_LENGTH);
        if ($violations->count() > 0) {
            Mage::throwException($violations->get(0)->getMessage());
        }

        return true;
    }
}
