<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Visitor getResource()
 */
class Mage_Log_Model_Visitor extends Mage_Core_Model_Abstract implements Mage_Log_Api_Data_VisitorInterface
{
    public const DEFAULT_ONLINE_MINUTES_INTERVAL = 15;
    public const VISITOR_TYPE_CUSTOMER = 'c';
    public const VISITOR_TYPE_VISITOR  = 'v';

    protected $_skipRequestLogging = false;

    /**
     * @var Mage_Log_Helper_Data
     */
    protected $_logCondition;

    /**
     * @var Mage_Core_Helper_Http
     */
    protected $_httpHelper;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Session
     */
    protected $_session;

    /**
     * Mage_Log_Model_Visitor constructor.
     */
    public function __construct(array $data = [])
    {
        $this->_httpHelper = !empty($data['http_helper']) ? $data['http_helper'] : Mage::helper('core/http');
        $this->_config = !empty($data['config']) ? $data['config'] : Mage::getConfig();
        $this->_logCondition = !empty($data['log_condition']) ?
            $data['log_condition'] : Mage::helper('log');
        $this->_session = !empty($data['session']) ? $data['session'] : Mage::getSingleton('core/session');
        parent::__construct($data);
    }

    /**
     * Object initialization
     */
    protected function _construct()
    {
        $this->_init('log/visitor');
        if ($this->_logCondition->isLogDisabled()) {
            $this->_skipRequestLogging = true;
            return;
        }

        $ignoreAgents = $this->_config->getNode('global/ignore_user_agents');
        if ($ignoreAgents) {
            $ignoreAgents = $ignoreAgents->asArray();
            $userAgent = $this->_httpHelper->getHttpUserAgent();
            foreach ($ignoreAgents as $ignoreAgent) {
                if (stripos($userAgent, $ignoreAgent) !== false) {
                    $this->_skipRequestLogging = true;
                    break;
                }
            }
        }
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    protected function _getSession()
    {
        return $this->_session;
    }

    /**
     * Initialize visitor information from server data
     *
     * @return $this
     */
    public function initServerData()
    {
        $this->addData([
            'server_addr'           => $this->_httpHelper->getServerAddr(true),
            'remote_addr'           => $this->_httpHelper->getRemoteAddr(true),
            'http_secure'           => Mage::app()->isCurrentlySecure(),
            'http_host'             => $this->_httpHelper->getHttpHost(true),
            'http_user_agent'       => $this->_httpHelper->getHttpUserAgent(true),
            'http_accept_language'  => $this->_httpHelper->getHttpAcceptLanguage(true),
            'http_accept_charset'   => $this->_httpHelper->getHttpAcceptCharset(true),
            'request_uri'           => $this->_httpHelper->getRequestUri(true),
            'session_id'            => $this->_session->getSessionId(),
            'http_referer'          => $this->_httpHelper->getHttpReferer(true),
        ]);

        return $this;
    }

    /**
     * Return Online Minutes Interval
     *
     * @return int Minutes Interval
     */
    public static function getOnlineMinutesInterval()
    {
        $configValue = Mage::getStoreConfig('customer/online_customers/online_minutes_interval');
        return (int) $configValue > 0
            ? (int) $configValue
            : self::DEFAULT_ONLINE_MINUTES_INTERVAL;
    }

    /**
     * Retrieve url from model data
     *
     * @return string
     */
    public function getUrl()
    {
        $url = 'http' . ($this->getHttpSecure() ? 's' : '') . '://';
        return $url . ($this->getHttpHost() . $this->getRequestUri());
    }

    /**
     * @api
     * @return string
     */
    public function getFirstVisitAt()
    {
        if (!$this->hasData(self::DATA_FIRST_VISIT_AT)) {
            $this->setData(self::DATA_FIRST_VISIT_AT, Varien_Date::now());
        }
        return $this->getDataByKey(self::DATA_FIRST_VISIT_AT);
    }

    /**
     * @api
     * @return $this
     */
    public function setFirstVisitAt(?string $value)
    {
        return $this->setData(self::DATA_FIRST_VISIT_AT, $value);
    }

    /**
     * @api
     * @return string
     */
    public function getLastVisitAt()
    {
        if (!$this->hasData(self::DATA_LAST_VISIT_AT)) {
            $this->setData(self::DATA_LAST_VISIT_AT, Varien_Date::now());
        }
        return $this->getDataByKey(self::DATA_LAST_VISIT_AT);
    }

    /**
     * @api
     * @return $this
     */
    public function setLastVisitAt(string $value)
    {
        return $this->setData(self::DATA_LAST_VISIT_AT, $value);
    }

    /**
     * Initialization visitor information by request
     *
     * Used in event "controller_action_predispatch"
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
     */
    public function initByRequest($observer)
    {
        if ($this->_skipRequestLogging || $this->isModuleIgnored($observer)) {
            return $this;
        }

        $this->setData($this->_session->getVisitorData());

        $visitorId = $this->getId();
        if (!$visitorId) {
            $this->initServerData();
            $this->setFirstVisitAt(Varien_Date::now());
            $this->setIsNewVisitor(true);
            $this->save();
        }
        if (!$visitorId || $this->_isVisitorSessionNew()) {
            Mage::dispatchEvent('visitor_init', ['visitor' => $this]);
        }
        return $this;
    }

    /**
     * Check is session new
     *
     * @return bool
     */
    protected function _isVisitorSessionNew()
    {
        $visitorData = $this->_session->getVisitorData();
        $visitorSessionId = null;
        if (is_array($visitorData) && isset($visitorData['session_id'])) {
            $visitorSessionId = $visitorData['session_id'];
        }
        return $this->_session->getSessionId() != $visitorSessionId;
    }

    /**
     * Saving visitor information by request
     *
     * Used in event "controller_action_postdispatch"
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
     */
    public function saveByRequest($observer)
    {
        if ($this->_skipRequestLogging || $this->isModuleIgnored($observer)) {
            return $this;
        }

        try {
            $this->initServerData();
            $this->setLastVisitAt(Varien_Date::now());
            $this->save();
            $this->_session->setVisitorData($this->getData());
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
        return $this;
    }

    /**
     * Bind customer data when customer login
     *
     * Used in event "customer_login"
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
     */
    public function bindCustomerLogin($observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getDataByKey('customer');
        if ($customer) {
            $this->setDoCustomerLogin(true);
            $this->setCustomerId($customer->getId());
        }
        return $this;
    }

    /**
     * Bind customer data when customer logout
     *
     * Used in event "customer_logout"
     *
     * @param   Varien_Event_Observer $observer
     * @return  $this
     */
    public function bindCustomerLogout($observer)
    {
        if ($this->getCustomerId() && $observer->getEvent()->getDataByKey('customer')) {
            $this->setDoCustomerLogout(true);
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function bindQuoteCreate($observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getDataByKey('quote');
        if ($quote) {
            if ($quote->getIsCheckoutCart()) {
                $this->setQuoteId($quote->getId());
                $this->setDoQuoteCreate(true);
            }
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function bindQuoteDestroy($observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getDataByKey('quote');
        if ($quote) {
            $this->setDoQuoteDestroy(true);
        }
        return $this;
    }

    /**
     * Methods for research (depends on customer online admin section)
     * @param Varien_Object $data
     * @return $this
     */
    public function addIpData($data)
    {
        $ipData = [];
        $data->setIpData($ipData);
        return $this;
    }

    /**
     * @param Varien_Object $data
     * @return $this
     */
    public function addCustomerData($data)
    {
        $customerId = $data->getCustomerId();
        if ((int) $customerId <= 0) {
            return $this;
        }
        $customerData = Mage::getModel('customer/customer')->load($customerId);
        $newCustomerData = [];
        foreach ($customerData->getData() as $propName => $propValue) {
            $newCustomerData['customer_' . $propName] = $propValue;
        }

        $data->addData($newCustomerData);
        return $this;
    }

    /**
     * @param Varien_Object $data
     * @return $this
     */
    public function addQuoteData($data)
    {
        $quoteId = $data->getQuoteId();
        if ((int) $quoteId <= 0) {
            return $this;
        }
        $data->setQuoteData(Mage::getModel('sales/quote')->load($quoteId));
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function isModuleIgnored($observer)
    {
        $ignores = $this->_config->getNode('global/ignoredModules/entities')->asArray();

        if (is_array($ignores) && $observer) {
            $curModule = $observer->getEvent()->getDataByKey('controller_action')->getRequest()->getRouteName();
            if (isset($ignores[$curModule])) {
                return true;
            }
        }
        return false;
    }

    public function getCustomerId(): ?int
    {
        return $this->getDataByKey('customer_id');
    }

    /**
     * @return $this
     */
    public function setCustomerId(?int $value)
    {
        return $this->setData('customer_id', $value);
    }

    public function getCustomerLogId(): ?int
    {
        return $this->getDataByKey('customer_log_id');
    }

    /**
     * @return $this
     */
    public function setCustomerLogId(?int $value)
    {
        return $this->setData('customer_log_id', $value);
    }

    public function getDoCustomerLogin(): int
    {
        return $this->getDataByKey('do_customer_login');
    }

    /**
     * @return $this
     */
    public function setDoCustomerLogin(?bool $value)
    {
        return $this->setData('do_customer_login', $value);
    }

    public function getDoCustomerLogout(): ?bool
    {
        return $this->getDataByKey('do_customer_logout');
    }

    /**
     * @return $this
     */
    public function setDoCustomerLogout(?bool $value)
    {
        return $this->setData('do_customer_logout', $value);
    }

    public function getDoQuoteCreate(): ?bool
    {
        return $this->getDataByKey('do_quote_create');
    }

    /**
     * @return $this
     */
    public function setDoQuoteCreate(?bool $value)
    {
        return $this->setData('do_quote_create', $value);
    }

    public function getIsNewVisitor(): bool
    {
        return $this->getDataByKey('is_new_visitor');
    }

    /**
     * @return $this
     */
    public function setIsNewVisitor(bool $value)
    {
        return $this->setData('is_new_visitor', $value);
    }

    public function getDoQuoteDestroy(): ?bool
    {
        return $this->getDataByKey('do_quote_destroy');
    }

    /**
     * @return $this
     */
    public function setDoQuoteDestroy(?bool $value)
    {
        return $this->setData('do_quote_destroy', $value);
    }

    public function getHttpAcceptCharset(): ?string
    {
        return $this->getDataByKey('http_accept_charset');
    }

    /**
     * @return $this
     */
    public function setHttpAcceptCharset(?string $value)
    {
        return $this->setData('http_accept_charset', $value);
    }

    public function getHttpAcceptLanguage(): ?string
    {
        return $this->getDataByKey('http_accept_language');
    }

    /**
     * @return $this
     */
    public function setHttpAcceptLanguage(?string $value)
    {
        return $this->setData('http_accept_language', $value);
    }

    public function getHttpHost(): ?string
    {
        return $this->getDataByKey('http_host');
    }

    /**
     * @return $this
     */
    public function setHttpHost(?string $value)
    {
        return $this->setData('http_host', $value);
    }

    public function getHttpReferer(): ?string
    {
        return $this->getDataByKey('http_referer');
    }

    /**
     * @return $this
     */
    public function setHttpReferer(?string $value)
    {
        return $this->setData('http_referer', $value);
    }

    public function getHttpSecure(): ?string
    {
        return $this->getDataByKey('http_secure');
    }

    /**
     * @return $this
     */
    public function setHttpSecure(?string $value)
    {
        return $this->setData('http_secure', $value);
    }

    public function getHttpUserAgent(): ?string
    {
        return $this->getDataByKey('http_user_agent');
    }

    /**
     * @return $this
     */
    public function setHttpUserAgent(?string $value)
    {
        return $this->setData('http_user_agent', $value);
    }

    public function getQuoteId(): ?int
    {
        return $this->getDataByKey('quote_id');
    }

    /**
     * @return $this
     */
    public function setQuoteId(?int $value)
    {
        return $this->setData('quote_id', $value);
    }

    public function getRemoteAddr(): ?string
    {
        return $this->getDataByKey('remote_addr');
    }

    /**
     * @return $this
     */
    public function setRemoteAddr(?string $value)
    {
        return $this->setData('remote_addr', $value);
    }

    public function getRequestUri(): ?string
    {
        return $this->getDataByKey('request_uri');
    }

    /**
     * @return $this
     */
    public function setRequestUri(?string $value)
    {
        return $this->setData('request_uri', $value);
    }

    public function getServerAddr(): ?string
    {
        return $this->getDataByKey('server_addr');
    }

    /**
     * @return $this
     */
    public function setServerAddr(?string $value)
    {
        return $this->setData('server_addr', $value);
    }

    /**
     * @api
     */
    public function getVisitorId(): ?int
    {
        $visitorId = $this->getDataByKey(self::DATA_ID);
        return is_null($visitorId) ? null : (int) $visitorId;
    }

    /**
     * @api
     * @return $this
     */
    public function setVisitorId(?int $value)
    {
        return $this->setData(self::DATA_ID, $value);
    }

    /**
     * @api
     */
    public function getLastUrlId(): int
    {
        return (int) $this->getDataByKey(self::DATA_LAST_URL_ID);
    }

    /**
     * @api
     * @return $this
     */
    public function setLastUrlId(int $value)
    {
        return $this->setData(self::DATA_LAST_URL_ID, $value);
    }

    /**
     * @api
     */
    public function getSessionId(): ?string
    {
        return $this->getDataByKey(self::DATA_SESSION_ID);
    }

    /**
     * @api
     * @return $this
     */
    public function setSessionId(?string $value)
    {
        return $this->setData(self::DATA_SESSION_ID, $value);
    }

    /**
     * @api
     */
    public function getStoreId(): int
    {
        return (int) $this->getDataByKey(self::DATA_STORE_ID);
    }

    /**
     * @api
     * @return $this
     */
    public function setStoreId(int $storeId)
    {
        return $this->setData(self::DATA_STORE_ID, $storeId);
    }
}
