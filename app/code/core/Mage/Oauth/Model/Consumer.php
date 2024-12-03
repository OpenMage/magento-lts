<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Application model
 *
 * @category   Mage
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
     * @throw Mage_Core_Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        /** @var Mage_Oauth_Model_Consumer_Validator_KeyLength $validatorLength */
        $validatorLength = Mage::getModel('oauth/consumer_validator_keyLength', ['length' => self::KEY_LENGTH]);

        $validatorLength->setName('Consumer Key');
        if (!$validatorLength->isValid($this->getKey())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::SECRET_LENGTH);
        $validatorLength->setName('Consumer Secret');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }
        return true;
    }
}
