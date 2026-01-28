<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * oAuth token model
 *
 * @package    Mage_Oauth
 *
 * @method Mage_Oauth_Model_Resource_Token            _getResource()
 * @method int                                        getAdminId()
 * @method int                                        getAuthorized()
 * @method string                                     getCallbackUrl()
 * @method Mage_Oauth_Model_Resource_Token_Collection getCollection()
 * @method int                                        getConsumerId()
 * @method int                                        getCustomerId()
 * @method Mage_Oauth_Model_Resource_Token            getResource()
 * @method Mage_Oauth_Model_Resource_Token_Collection getResourceCollection()
 * @method int                                        getRevoked()
 * @method string                                     getSecret()
 * @method string                                     getToken()
 * @method string                                     getType()
 * @method string                                     getVerifier()
 * @method string                                     getName() Consumer name (joined from consumer table)
 * @method $this                                      setAdminId(int $adminId)
 * @method $this                                      setAuthorized(int $authorized)
 * @method $this                                      setCallbackUrl(string $callbackUrl)
 * @method $this                                      setConsumerId(int $consumerId)
 * @method $this                                      setCustomerId(int $customerId)
 * @method $this                                      setRevoked(int $revoked)
 * @method $this                                      setSecret(string $tokenSecret)
 * @method $this                                      setToken(string $token)
 * @method $this                                      setType(string $type)
 * @method $this                                      setVerifier(string $verifier)
 */
class Mage_Oauth_Model_Token extends Mage_Core_Model_Abstract
{
    /**
     * Token types
     */
    public const TYPE_REQUEST = 'request';

    public const TYPE_ACCESS  = 'access';

    /**
     * Lengths of token fields
     */
    public const LENGTH_TOKEN    = 32;

    public const LENGTH_SECRET   = 32;

    public const LENGTH_VERIFIER = 32;

    /**
     * Customer types
     */
    public const USER_TYPE_ADMIN    = 'admin';

    public const USER_TYPE_CUSTOMER = 'customer';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }

    /**
     * "After save" actions
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }

        return $this;
    }

    /**
     * Authorize token
     *
     * @param  int    $userId   Authorization user identifier
     * @param  string $userType Authorization user type
     * @return $this
     */
    public function authorize($userId, $userType)
    {
        if (!$this->getId() || !$this->getConsumerId()) {
            Mage::throwException('Token is not ready to be authorized');
        }

        if ($this->getAuthorized()) {
            Mage::throwException('Token is already authorized');
        }

        if (self::USER_TYPE_ADMIN == $userType) {
            $this->setAdminId($userId);
        } elseif (self::USER_TYPE_CUSTOMER == $userType) {
            $this->setCustomerId($userId);
        } else {
            Mage::throwException('User type is unknown');
        }

        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        $this->setVerifier($helper->generateVerifier());
        $this->setAuthorized(1);
        $this->save();

        $this->getResource()->cleanOldAuthorizedTokensExcept($this);

        return $this;
    }

    /**
     * Convert token to access type
     *
     * @return $this
     */
    public function convertToAccess()
    {
        if (self::TYPE_REQUEST != $this->getType()) {
            Mage::throwException('Can not convert due to token is not request type');
        }

        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        $this->setType(self::TYPE_ACCESS);
        $this->setToken($helper->generateToken());
        $this->setSecret($helper->generateTokenSecret());
        $this->save();

        return $this;
    }

    /**
     * Generate and save request token
     *
     * @param  int    $consumerId  Consumer identifier
     * @param  string $callbackUrl Callback URL
     * @return $this
     */
    public function createRequestToken($consumerId, $callbackUrl)
    {
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        $this->setData([
            'consumer_id'  => $consumerId,
            'type'         => self::TYPE_REQUEST,
            'token'        => $helper->generateToken(),
            'secret'       => $helper->generateTokenSecret(),
            'callback_url' => $callbackUrl,
        ]);
        $this->save();

        return $this;
    }

    /**
     * Get OAuth user type
     *
     * @return string
     * @throws Exception
     */
    public function getUserType()
    {
        if ($this->getAdminId()) {
            return self::USER_TYPE_ADMIN;
        }

        if ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        }

        Mage::throwException('User type is unknown');
    }

    /**
     * Get string representation of token
     *
     * @param  string $format
     * @return string
     */
    public function toString($format = '')
    {
        return http_build_query(['oauth_token' => $this->getToken(), 'oauth_token_secret' => $this->getSecret()]);
    }

    /**
     * Before save actions
     *
     * @return Mage_Oauth_Model_Token
     */
    protected function _beforeSave()
    {
        $this->validate();

        if ($this->isObjectNew() && $this->getCreatedAt() === null) {
            $this->setCreatedAt(Varien_Date::now());
        }

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

        $callback = $this->getCallbackUrl();

        if (Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED !== $callback) {
            $callbackUrl = $this->getConsumer()->getCallbackUrl();
            $isWhitelisted = $callbackUrl && str_starts_with($callback, $callbackUrl);
            $violations = $validator->validateUrl(
                value: $callback,
                message: 'Invalid URL {{ value }}.',
            );
            if (!$isWhitelisted && $violations->count() > 0) {
                Mage::throwException($violations->get(0)->getMessage());
            }
        }

        $violations = $validator->validateLength(value: $this->getSecret(), exactly: self::LENGTH_SECRET);
        if ($violations->count() > 0) {
            Mage::throwException($violations->get(0)->getMessage());
        }

        $violations = $validator->validateLength(value: $this->getToken(), exactly: self::LENGTH_TOKEN);
        if ($violations->count() > 0) {
            Mage::throwException($violations->get(0)->getMessage());
        }

        $verifier = $this->getVerifier();
        if ($verifier !== null) {
            $violations = $validator->validateLength(value: $verifier, exactly: self::LENGTH_VERIFIER);
            if ($violations->count() > 0) {
                Mage::throwException($violations->get(0)->getMessage());
            }
        }

        return true;
    }

    /**
     * Get Token Consumer
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getConsumer()
    {
        if (!$this->getData('consumer')) {
            /** @var Mage_Oauth_Model_Consumer $consumer */
            $consumer = Mage::getModel('oauth/consumer');
            $consumer->load($this->getConsumerId());
            $this->setData('consumer', $consumer);
        }

        return $this->getData('consumer');
    }
}
