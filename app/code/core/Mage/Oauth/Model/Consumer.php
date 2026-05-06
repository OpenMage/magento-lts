<?php

declare(strict_types=1);

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
 * @method Mage_Oauth_Model_Resource_Consumer            _getResource()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Consumer            getResource()
 * @method Mage_Oauth_Model_Resource_Consumer_Collection getResourceCollection()
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

    public function getCallbackUrl(): string
    {
        return (string) $this->_getData('callback_url');
    }

    public function getKey(): string
    {
        return (string) $this->_getData('key');
    }

    public function getName(): string
    {
        return (string) $this->_getData('name');
    }

    public function getRejectedCallbackUrl(): string
    {
        return (string) $this->_getData('rejected_callback_url');
    }

    public function getSecret(): string
    {
        return (string) $this->_getData('secret');
    }

    public function setCallbackUrl(string $value): static
    {
        return $this->setData('callback_url', $value);
    }

    public function setKey(string $value): static
    {
        return $this->setData('key', $value);
    }

    public function setName(string $value): static
    {
        return $this->setData('name', $value);
    }

    public function setRejectedCallbackUrl(string $value): static
    {
        return $this->setData('rejected_callback_url', $value);
    }

    public function setSecret(string $value): static
    {
        return $this->setData('secret', $value);
    }

    /**
     * BeforeSave actions
     *
     * @return $this
     */
    #[Override]
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setUpdatedAt($this->getClockHelper()->getTimestamp());
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
