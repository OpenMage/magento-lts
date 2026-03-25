<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

use Carbon\Carbon;

/**
 * Application model
 *
 * @package    Mage_Oauth
 *
 * @method Mage_Oauth_Model_Resource_Consumer            _getResource()
 * @method string                                        getCallbackUrl()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getCollection()
 * @method string                                        getKey()
 * @method string                                        getName()
 * @method string                                        getRejectedCallbackUrl()
 * @method Mage_Oauth_Model_Resource_Consumer            getResource()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getResourceCollection()
 * @method string                                        getSecret()
 * @method $this                                         setCallbackUrl(string $url)
 * @method $this                                         setKey(string $key)
 * @method $this                                         setName(string $name)
 * @method $this                                         setRejectedCallbackUrl(string $rejectedCallbackUrl)
 * @method $this                                         setSecret(string $secret)
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

    /**
     * @inheritDoc
     */
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
            $this->setUpdatedAt(Carbon::now()->getTimestamp());
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
     * @throws Mage_Core_Exception Throw exception on fail validation
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
