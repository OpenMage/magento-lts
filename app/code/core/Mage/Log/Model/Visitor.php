<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Visitor _getResource()
 * @method bool                            getDoCustomerLogin()
 * @method bool                            getDoCustomerLogout()
 * @method bool                            getDoQuoteCreate()
 * @method bool                            getDoQuoteDestroy()
 * @method bool                            getIsNewVisitor()
 * @method Mage_Log_Model_Resource_Visitor getResource()
 * @method $this                           setDoCustomerLogin(bool $value)
 * @method $this                           setDoCustomerLogout(bool $value)
 * @method $this                           setDoQuoteCreate(bool $value)
 * @method $this                           setDoQuoteDestroy(bool $value)
 * @method $this                           setIsNewVisitor(bool $value)
 * @method $this                           setQuoteId(int $value)
 */
class Mage_Log_Model_Visitor extends Mage_Core_Model_Abstract
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
        $this->_httpHelper = empty($data['http_helper']) ? Mage::helper('core/http') : $data['http_helper'];
        $this->_config = empty($data['config']) ? Mage::getConfig() : $data['config'];
        $this->_logCondition = empty($data['log_condition'])
            ? Mage::helper('log') : $data['log_condition'];
        $this->_session = empty($data['session']) ? Mage::getSingleton('core/session') : $data['session'];
        parent::__construct($data);
    }

    /**
     * @inheritDoc
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
                if (stripos($userAgent, (string) $ignoreAgent) !== false) {
                    $this->_skipRequestLogging = true;
                    break;
                }
            }
        }
    }

    public function getLastUrlId(): int
    {
        return (int) $this->_getData('last_url_id');
    }

    public function getSessionId(): ?string
    {
        $value = $this->_getData('session_id');
        return $value !== null ? (string) $value : null;
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setFirstVisitAt(string $value): static
    {
        return $this->setData('first_visit_at', $value);
    }

    public function setLastUrlId(int $value): static
    {
        return $this->setData('last_url_id', $value);
    }

    public function setLastVisitAt(string $value): static
    {
        return $this->setData('last_visit_at', $value);
    }

    public function setSessionId(?string $value): static
    {
        return $this->setData('session_id', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
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
            'http_secure'           => Mage::app()->getStore()->isCurrentlySecure(),
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
     * @return string
     */
    public function getFirstVisitAt(): ?string
    {
        if (!$this->hasData('first_visit_at')) {
            $this->setData('first_visit_at', Varien_Date::now());
        }

        return $this->getDataByKey('first_visit_at');
    }

    /**
     * @return string
     */
    public function getLastVisitAt(): string
    {
        if (!$this->hasData('last_visit_at')) {
            $this->setData('last_visit_at', Varien_Date::now());
        }

        return (string) $this->getDataByKey('last_visit_at');
    }

    /**
     * Initialization visitor information by request
     *
     * Used in event "controller_action_predispatch"
     *
     * @param  Varien_Event_Observer $observer
     * @return $this
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
     * @param  Varien_Event_Observer $observer
     * @return $this
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
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function bindCustomerLogin($observer)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
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
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function bindCustomerLogout($observer)
    {
        if ($this->getCustomerId() && $observer->getEvent()->getCustomer()) {
            $this->setDoCustomerLogout(true);
        }

        return $this;
    }

    /**
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function bindQuoteCreate($observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote && $quote->getIsCheckoutCart()) {
            $this->setQuoteId($quote->getId());
            $this->setDoQuoteCreate(true);
        }

        return $this;
    }

    /**
     * @param  Varien_Event_Observer $observer
     * @return $this
     */
    public function bindQuoteDestroy($observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote) {
            $this->setDoQuoteDestroy(true);
        }

        return $this;
    }

    /**
     * Methods for research (depends on customer online admin section)
     * @param  Varien_Object $data
     * @return $this
     */
    public function addIpData($data)
    {
        $ipData = [];
        $data->setIpData($ipData);
        return $this;
    }

    /**
     * @param  Varien_Object $data
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
     * @param  Varien_Object $data
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
     * @param  Varien_Event_Observer $observer
     * @return bool
     */
    public function isModuleIgnored($observer)
    {
        $ignores = $this->_config->getNode('global/ignoredModules/entities')->asArray();

        if (is_array($ignores) && $observer) {
            $curModule = $observer->getEvent()->getControllerAction()->getRequest()->getRouteName();
            if (isset($ignores[$curModule])) {
                return true;
            }
        }

        return false;
    }

    public function getCustomerId(): ?int
    {
        $value = $this->_getData('customer_id');
        return $value !== null ? (int) $value : null;
    }

    public function setCustomerId(int $value): static
    {
        return $this->setData('customer_id', $value);
    }

    public function getCustomerLogId(): ?int
    {
        $value = $this->_getData('customer_log_id');
        return $value !== null ? (int) $value : null;
    }

    public function setCustomerLogId(int $value): static
    {
        return $this->setData('customer_log_id', $value);
    }

    public function getHttpAcceptCharset(): ?string
    {
        $value = $this->_getData('http_accept_charset');
        return $value !== null ? (string) $value : null;
    }

    public function getHttpAcceptLanguage(): ?string
    {
        $value = $this->_getData('http_accept_language');
        return $value !== null ? (string) $value : null;
    }

    public function getHttpHost(): ?string
    {
        $value = $this->_getData('http_host');
        return $value !== null ? (string) $value : null;
    }

    public function getHttpReferer(): ?string
    {
        $value = $this->_getData('http_referer');
        return $value !== null ? (string) $value : null;
    }

    public function getHttpSecure(): ?string
    {
        $value = $this->_getData('http_secure');
        return $value !== null ? (string) $value : null;
    }

    public function getHttpUserAgent(): ?string
    {
        $value = $this->_getData('http_user_agent');
        return $value !== null ? (string) $value : null;
    }

    public function getQuoteId(): ?int
    {
        $value = $this->_getData('quote_id');
        return $value !== null ? (int) $value : null;
    }

    public function getRemoteAddr(): ?string
    {
        $value = $this->_getData('remote_addr');
        return $value !== null ? (string) $value : null;
    }

    public function getRequestUri(): ?string
    {
        $value = $this->_getData('request_uri');
        return $value !== null ? (string) $value : null;
    }

    public function getServerAddr(): ?string
    {
        $value = $this->_getData('server_addr');
        return $value !== null ? (string) $value : null;
    }

    public function getVisitorId(): ?int
    {
        $value = $this->_getData('visitor_id');
        return $value !== null ? (int) $value : null;
    }
}
