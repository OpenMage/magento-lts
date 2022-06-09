<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth token model
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method string getName() Consumer name (joined from consumer table)
 * @method Mage_Oauth_Model_Resource_Token_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Token_Collection getResourceCollection()
 * @method Mage_Oauth_Model_Resource_Token getResource()
 * @method Mage_Oauth_Model_Resource_Token _getResource()
 * @method int getConsumerId()
 * @method Mage_Oauth_Model_Token setConsumerId() setConsumerId(int $consumerId)
 * @method int getAdminId()
 * @method Mage_Oauth_Model_Token setAdminId() setAdminId(int $adminId)
 * @method int getCustomerId()
 * @method Mage_Oauth_Model_Token setCustomerId() setCustomerId(int $customerId)
 * @method string getType()
 * @method Mage_Oauth_Model_Token setType() setType(string $type)
 * @method string getVerifier()
 * @method Mage_Oauth_Model_Token setVerifier() setVerifier(string $verifier)
 * @method string getCallbackUrl()
 * @method Mage_Oauth_Model_Token setCallbackUrl() setCallbackUrl(string $callbackUrl)
 * @method string getCreatedAt()
 * @method Mage_Oauth_Model_Token setCreatedAt() setCreatedAt(string $createdAt)
 * @method string getToken()
 * @method Mage_Oauth_Model_Token setToken() setToken(string $token)
 * @method string getSecret()
 * @method Mage_Oauth_Model_Token setSecret() setSecret(string $tokenSecret)
 * @method int getRevoked()
 * @method Mage_Oauth_Model_Token setRevoked() setRevoked(int $revoked)
 * @method int getAuthorized()
 * @method Mage_Oauth_Model_Token setAuthorized() setAuthorized(int $authorized)
 */
class Mage_Oauth_Model_Token extends Mage_Core_Model_Abstract
{
    /**#@+
     * Token types
     */
    const TYPE_REQUEST = 'request';
    const TYPE_ACCESS  = 'access';
    /**#@- */

    /**#@+
     * Lengths of token fields
     */
    const LENGTH_TOKEN    = 32;
    const LENGTH_SECRET   = 32;
    const LENGTH_VERIFIER = 32;
    /**#@- */

    /**#@+
     * Customer types
     */
    const USER_TYPE_ADMIN    = 'admin';
    const USER_TYPE_CUSTOMER = 'customer';
    /**#@- */

    /**
     * Initialize resource model
     *
     * @return void
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
     * @param int $userId Authorization user identifier
     * @param string $userType Authorization user type
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
        if (Mage_Oauth_Model_Token::TYPE_REQUEST != $this->getType()) {
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
     * @param int $consumerId Consumer identifier
     * @param string $callbackUrl Callback URL
     * @return $this
     */
    public function createRequestToken($consumerId, $callbackUrl)
    {
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        $this->setData(array(
            'consumer_id'  => $consumerId,
            'type'         => self::TYPE_REQUEST,
            'token'        => $helper->generateToken(),
            'secret'       => $helper->generateTokenSecret(),
            'callback_url' => $callbackUrl
        ));
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
        } elseif ($this->getCustomerId()) {
            return self::USER_TYPE_CUSTOMER;
        } else {
            Mage::throwException('User type is unknown');
        }
    }

    /**
     * Get string representation of token
     *
     * @param string $format
     * @return string
     */
    public function toString($format = '')
    {
        return http_build_query(array('oauth_token' => $this->getToken(), 'oauth_token_secret' => $this->getSecret()));
    }

    /**
     * Before save actions
     *
     * @return Mage_Oauth_Model_Token
     */
    protected function _beforeSave()
    {
        $this->validate();

        if ($this->isObjectNew() && null === $this->getCreatedAt()) {
            $this->setCreatedAt(Varien_Date::now());
        }
        parent::_beforeSave();
        return $this;
    }

    /**
     * Validate data
     *
     * @return array|bool
     * @throw Mage_Core_Exception|Exception   Throw exception on fail validation
     */
    public function validate()
    {
        /** @var Mage_Core_Model_Url_Validator $validatorUrl */
        $validatorUrl = Mage::getSingleton('core/url_validator');
        if (Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED != $this->getCallbackUrl()
            && !$validatorUrl->isValid($this->getCallbackUrl())
        ) {
            $messages = $validatorUrl->getMessages();
            Mage::throwException(array_shift($messages));
        }

        /** @var Mage_Oauth_Model_Consumer_Validator_KeyLength $validatorLength */
        $validatorLength = Mage::getModel(
            'oauth/consumer_validator_keyLength'
        );
        $validatorLength->setLength(self::LENGTH_SECRET);
        $validatorLength->setName('Token Secret Key');
        if (!$validatorLength->isValid($this->getSecret())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        $validatorLength->setLength(self::LENGTH_TOKEN);
        $validatorLength->setName('Token Key');
        if (!$validatorLength->isValid($this->getToken())) {
            $messages = $validatorLength->getMessages();
            Mage::throwException(array_shift($messages));
        }

        if (null !== ($verifier = $this->getVerifier())) {
            $validatorLength->setLength(self::LENGTH_VERIFIER);
            $validatorLength->setName('Verifier Key');
            if (!$validatorLength->isValid($verifier)) {
                $messages = $validatorLength->getMessages();
                Mage::throwException(array_shift($messages));
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
