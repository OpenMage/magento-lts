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
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth Helper
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Endpoint types with appropriate routes
     */
    public const ENDPOINT_AUTHORIZE_CUSTOMER        = 'oauth/authorize';
    public const ENDPOINT_AUTHORIZE_ADMIN           = 'adminhtml/oauth_authorize';
    public const ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE = 'oauth/authorize/simple';
    public const ENDPOINT_AUTHORIZE_ADMIN_SIMPLE    = 'adminhtml/oauth_authorize/simple';
    public const ENDPOINT_INITIATE                  = 'oauth/initiate';
    public const ENDPOINT_TOKEN                     = 'oauth/token';

    /**
     * Cleanup xpath config settings
     */
    public const XML_PATH_CLEANUP_PROBABILITY       = 'oauth/cleanup/cleanup_probability';
    public const XML_PATH_CLEANUP_EXPIRATION_PERIOD = 'oauth/cleanup/expiration_period';

    /** Email template */
    public const XML_PATH_EMAIL_TEMPLATE = 'oauth/email/template';
    public const XML_PATH_EMAIL_IDENTITY = 'oauth/email/identity';

    /**
     * Cleanup expiration period in minutes
     */
    public const CLEANUP_EXPIRATION_PERIOD_DEFAULT = 120;

    /**
     * Query parameter as a sign that user rejects
     */
    public const QUERY_PARAM_REJECTED = 'rejected';

    protected $_moduleName = 'Mage_Oauth';

    /**
     * Available endpoints list
     *
     * @var array
     */
    protected $_endpoints = [
        self::ENDPOINT_AUTHORIZE_CUSTOMER,
        self::ENDPOINT_AUTHORIZE_ADMIN,
        self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE,
        self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE,
        self::ENDPOINT_INITIATE,
        self::ENDPOINT_TOKEN,
    ];

    /**
     * Generate random string for token or secret or verifier
     *
     * @param int $length String length
     * @return string
     */
    protected function _generateRandomString($length)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            // use openssl lib if it is install. It provides a better randomness
            $bytes = openssl_random_pseudo_bytes((int) ceil($length / 2), $strong);
            $hex = bin2hex($bytes); // hex() doubles the length of the string
            $randomString = substr($hex, 0, $length); // we truncate at most 1 char if length parameter is an odd number
        } else {
            // fallback to mt_rand() if openssl is not installed
            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');
            $randomString = $helper->getRandomString(
                $length,
                Mage_Core_Helper_Data::CHARS_DIGITS . Mage_Core_Helper_Data::CHARS_LOWERS,
            );
        }

        return $randomString;
    }

    /**
     * Generate random string for token
     *
     * @return string
     */
    public function generateToken()
    {
        return $this->_generateRandomString(Mage_Oauth_Model_Token::LENGTH_TOKEN);
    }

    /**
     * Generate random string for token secret
     *
     * @return string
     */
    public function generateTokenSecret()
    {
        return $this->_generateRandomString(Mage_Oauth_Model_Token::LENGTH_SECRET);
    }

    /**
     * Generate random string for verifier
     *
     * @return string
     */
    public function generateVerifier()
    {
        return $this->_generateRandomString(Mage_Oauth_Model_Token::LENGTH_VERIFIER);
    }

    /**
     * Generate random string for consumer key
     *
     * @return string
     */
    public function generateConsumerKey()
    {
        return $this->_generateRandomString(Mage_Oauth_Model_Consumer::KEY_LENGTH);
    }

    /**
     * Generate random string for consumer secret
     *
     * @return string
     */
    public function generateConsumerSecret()
    {
        return $this->_generateRandomString(Mage_Oauth_Model_Consumer::SECRET_LENGTH);
    }

    /**
     * Return complete callback URL or boolean FALSE if no callback provided
     *
     * @param Mage_Oauth_Model_Token $token Token object
     * @param bool $rejected OPTIONAL Add user reject sign
     * @return bool|string
     */
    public function getFullCallbackUrl(Mage_Oauth_Model_Token $token, $rejected = false)
    {
        $callbackUrl = $token->getCallbackUrl();

        if (Mage_Oauth_Model_Server::CALLBACK_ESTABLISHED == $callbackUrl) {
            return false;
        }
        if ($rejected) {
            /** @var Mage_Oauth_Model_Consumer $consumer */
            $consumer = Mage::getModel('oauth/consumer')->load($token->getConsumerId());

            if ($consumer->getId() && $consumer->getRejectedCallbackUrl()) {
                $callbackUrl = $consumer->getRejectedCallbackUrl();
            }
        } elseif (!$token->getAuthorized()) {
            Mage::throwException('Token is not authorized');
        }
        $callbackUrl .= (!str_contains($callbackUrl, '?') ? '?' : '&');
        $callbackUrl .= 'oauth_token=' . $token->getToken() . '&';

        return $callbackUrl . ($rejected ? self::QUERY_PARAM_REJECTED . '=1' : 'oauth_verifier=' . $token->getVerifier());
    }

    /**
     * Retrieve URL of specified endpoint.
     *
     * @param string $type Endpoint type (one of ENDPOINT_ constants)
     * @return string
     * @throws Exception    Exception when endpoint not found
     */
    public function getProtocolEndpointUrl($type)
    {
        if (!in_array($type, $this->_endpoints)) {
            throw new Exception('Invalid endpoint type passed.');
        }
        return rtrim(Mage::getUrl($type), '/');
    }

    /**
     * Calculate cleanup possibility for data with lifetime property
     *
     * @return bool
     */
    public function isCleanupProbability()
    {
        // Safe get cleanup probability value from system configuration
        $configValue = Mage::getStoreConfigAsInt(self::XML_PATH_CLEANUP_PROBABILITY);
        return $configValue > 0 ? mt_rand(1, $configValue) == 1 : false;
    }

    /**
     * Get cleanup expiration period value from system configuration in minutes
     *
     * @return int
     */
    public function getCleanupExpirationPeriod()
    {
        $minutes = Mage::getStoreConfigAsInt(self::XML_PATH_CLEANUP_EXPIRATION_PERIOD);
        return $minutes > 0 ? $minutes : self::CLEANUP_EXPIRATION_PERIOD_DEFAULT;
    }

    /**
     * Send Email to Token owner
     *
     * @param string $userEmail
     * @param string $userName
     * @param string $applicationName
     * @param string $status
     */
    public function sendNotificationOnTokenStatusChange($userEmail, $userName, $applicationName, $status)
    {
        /** @var Mage_Core_Model_Email_Template $mailTemplate */
        $mailTemplate = Mage::getModel('core/email_template');

        $mailTemplate->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY),
            $userEmail,
            $userName,
            [
                'name'              => $userName,
                'email'             => $userEmail,
                'applicationName'   => $applicationName,
                'status'            => $status,

            ],
        );
    }

    /**
     * Is current authorize page is simple
     *
     * @return bool
     */
    protected function _getIsSimple()
    {
        $simple = false;
        if (stristr($this->_getRequest()->getActionName(), 'simple')
            || !is_null($this->_getRequest()->getParam('simple', null))
        ) {
            $simple = true;
        }

        return $simple;
    }

    /**
     * Get authorize endpoint url
     *
     * @param string $userType
     * @return string
     */
    public function getAuthorizeUrl($userType)
    {
        $simple = $this->_getIsSimple();

        if (Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_CUSTOMER;
            }
        } elseif (Mage_Oauth_Model_Token::USER_TYPE_ADMIN == $userType) {
            if ($simple) {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN_SIMPLE;
            } else {
                $route = self::ENDPOINT_AUTHORIZE_ADMIN;
            }
        } else {
            throw new Exception('Invalid user type.');
        }

        return $this->_getUrl($route, ['_query' => ['oauth_token' => $this->getOauthToken()]]);
    }

    /**
     * Retrieve oauth_token param from request
     *
     * @return string|null
     */
    public function getOauthToken()
    {
        return $this->_getRequest()->getParam('oauth_token', null);
    }
}
