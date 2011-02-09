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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Push title length
     *
     * @var int
     */
    const PUSH_TITLE_LENGTH = 140;

    /**
     * Message title length
     *
     * @var int
     */
    const MESSAGE_TITLE_LENGTH = 255;

    protected $_excludedXmlConfigKeys = array(
        'notifications/applicationMasterSecret',
    );

    /**
     * Application names array
     *
     * @var array
     */
    protected $_appNames = array();

    /**
     * Template names array
     *
     * @var array
     */
    protected $_tplNames = array();

    /**
     * XML path to nodes to be excluded
     *
     * @var string
     */
    const XML_NODE_CONFIG_EXCLUDE_FROM_XML = 'xmlconnect/mobile_application/nodes_excluded_from_config_xml';

    /**
     * Default device type
     *
     * @var string
     */
    const DEVICE_TYPE_DEFAULT = 'unknown';

    /**
     * iPhone device identifier
     *
     * @var string
     */
    const DEVICE_TYPE_IPHONE = 'iphone';

    /**
     * iPad device identifier
     *
     * @var string
     */
    const DEVICE_TYPE_IPAD = 'ipad';

    /**
     * Android device identifier
     *
     * @var string
     */
    const DEVICE_TYPE_ANDROID = 'android';

    /**
     * Get device helper
     *
     * @throws Mage_Core_Exception
     * @param Mage_XmlConnect_Model_Application $application
     * @return Mage_Core_Helper_Abstract
     */
    public function getDeviceHelper($application = null)
    {
        $deviceType = $this->getDeviceType($application);

        switch ($deviceType) {
            case self::DEVICE_TYPE_IPHONE:
            case self::DEVICE_TYPE_IPAD:
            case self::DEVICE_TYPE_ANDROID:
                $helper =  Mage::helper('xmlconnect/' . $deviceType);
                break;
            default:
                Mage::throwException(Mage::helper('xmlconnect')->__('Device doesn\'t recognized: "%s". Unable to load a helper.', $deviceType));
                break;
        }
        return $helper;
    }

    /**
     * Get device tipe from application
     *
     * @param Mage_XmlConnect_Model_Application $application
     * @return string
     */
    public function getDeviceType($application = null)
    {
        $deviceType = null;
        if (empty($application) && Mage::registry('current_app') !== null) {
            $deviceType = (string) $this->getApplication()->getType();
        } elseif ($application instanceof Mage_XmlConnect_Model_Application) {
            $deviceType = (string) $application->getType();
        }
        if (empty($deviceType)) {
            $deviceType = self::DEVICE_TYPE_DEFAULT;
        }
        return $deviceType;
    }

    /**
     * Getter for current loaded application model
     *
     * @throws Mage_Core_Exception
     * @return Mage_XmlConnect_Model_Application
     */
    public function getApplication()
    {
        $model = Mage::registry('current_app');
        if (!($model instanceof Mage_XmlConnect_Model_Application)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('App model not loaded.'));
        }

        return $model;
    }

    /**
     * Create filter object by key
     *
     * @param string $key
     * @return Mage_Catalog_Model_Layer_Filter_Abstract
     */
    public function getFilterByKey($key)
    {
        switch ($key) {
            case 'price':
                $filterModelName = 'catalog/layer_filter_price';
                break;
            case 'decimal':
                $filterModelName = 'catalog/layer_filter_decimal';
                break;
            case 'category':
                $filterModelName = 'catalog/layer_filter_category';
                break;
            default:
                $filterModelName = 'catalog/layer_filter_attribute';
                break;
        }
        return Mage::getModel($filterModelName);
    }

    /**
     * Export $this->_getUrl() function to public
     *
     * @param string $route
     * @param array $params
     * @return array
     */
    public function getUrl($route, $params = array())
    {
        return $this->_getUrl($route, $params);
    }

    /**
     * Retrieve country options array
     *
     * @param bool $isItunes
     * @return array
     */
    public function getCountryOptionsArray($isItunes = false)
    {
        Varien_Profiler::start('TEST: '.__METHOD__);
        switch ($this->getDeviceType()) {
            case self::DEVICE_TYPE_IPHONE:
            case self::DEVICE_TYPE_IPAD:
                $cacheKey = 'XMLCONNECT_COUNTRY_ITUNES_SELECT_STORE_'.Mage::app()->getStore()->getCode();
                $itunesCountries = $this->getDeviceHelper()->getItunesCountriesArray();
                break;
            case self::DEVICE_TYPE_ANDROID:
            default:
                $cacheKey = 'XMLCONNECT_COUNTRY_SELECT_STORE_'.Mage::app()->getStore()->getCode();
                break;
        }

        if (false && Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            if (isset($itunesCountries)) {
                $options = Mage::getModel('directory/country')
                    ->getResourceCollection()
                    ->addFieldToFilter('country_id', array('in' => $itunesCountries))
                    ->loadByStore()
                    ->toOptionArray();
            } else {
                $options = Mage::getModel('directory/country')
                    ->getResourceCollection()
                    ->loadByStore()
                    ->toOptionArray();
            }
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        Varien_Profiler::stop('TEST: '.__METHOD__);
        return $options;
    }

    /**
     * Get list of predefined and supported Devices
     *
     * @return array
     */
    static public function getSupportedDevices()
    {
        $devices = array (
            self::DEVICE_TYPE_IPAD      => Mage::helper('xmlconnect')->__('iPad'),
            self::DEVICE_TYPE_IPHONE    => Mage::helper('xmlconnect')->__('iPhone'),
            self::DEVICE_TYPE_ANDROID   => Mage::helper('xmlconnect')->__('Android')
        );

        return $devices;
    }

    /**
     * Get list of predefined and supported Devices
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $options = array (
            Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS => Mage::helper('xmlconnect')->__('Submitted'),
            Mage_XmlConnect_Model_Application::APP_STATUS_INACTIVE => Mage::helper('xmlconnect')->__('Not Submitted'),
        );
        return $options;
    }

    /**
     * Retrieve supported device types as "html select options"
     *
     * @return array
     */
    public function getDeviceTypeOptions()
    {
        $devices = self::getSupportedDevices();
        $options = array();
        if (count($devices) > 1) {
            $options[] = array('value' => '', 'label' => Mage::helper('xmlconnect')->__('Please Select Device Type'));
        }
        foreach ($devices as $type => $label) {
            $options[] = array('value' => $type, 'label' => $label);
        }
        return $options;
    }

    /**
     * Get default application tabs
     *
     * @param string
     * @return array
     */
    public function getDefaultApplicationDesignTabs()
    {
        return $this->getDeviceHelper()->getDefaultDesignTabs();
    }

    /**
     * Return array for tabs like  label -> action array
     *
     * @return array
     */
    protected function _getTabLabelActionArray()
    {
        if (!isset($this->_tabLabelActionArray)) {
            $this->_tabLabelActionArray = array();
            foreach ($this->getDefaultApplicationDesignTabs() as $tab) {
                $this->_tabLabelActionArray[$tab['action']] = $tab['label'];
            }
        }
        return $this->_tabLabelActionArray;
    }

    /**
     * Return Translated tab label for given $action
     *
     * @param string $action
     * @return string|bool
     */
    public function getTabLabel($action)
    {
        $action = (string) $action;
        $tabs = $this->_getTabLabelActionArray();
        return (isset($tabs[$action])) ? $tabs[$action] : false;
    }

    /**
     * Merges $changes array to $target array recursive, overwriting existing key,  and adding new one
     *
     * @param mixed $target
     * @param mixed $changes
     * @return array
     */
    static public function arrayMergeRecursive($target, $changes)
    {
        if (!is_array($target)) {
            $target = empty($target) ? array() : array($target);
        }
        if (!is_array($changes)) {
            $changes = array($changes);
        }
        foreach ($changes as $key => $value) {
            if (!array_key_exists($key, $target) and !is_numeric($key)) {
                $target[$key] = $changes[$key];
                continue;
            }
            if (is_array($value) or is_array($target[$key])) {
                $target[$key] = self::arrayMergeRecursive($target[$key], $changes[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $target)) {
                    $target[] = $value;
                }
            } else {
                $target[$key] = $value;
            }
        }

        return $target;
    }

    /**
     * Wrap $body with HTML4 headers
     *
     * @param string $body
     * @return string
     */
    public function htmlize($body)
    {
        return <<<EOT
&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;
&lt;!DOCTYPE html PUBLIC &quot;-//W3C//DTD XHTML 1.0 Strict//EN&quot; &quot;http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd&quot;&gt;
&lt;html xmlns=&quot;http://www.w3.org/1999/xhtml&quot; xml:lang=&quot;en&quot; lang=&quot;en&quot;&gt;
&lt;head&gt;
&lt;link rel=&quot;stylesheet&quot; type=&quot;text/css&quot; href=&quot;style.css&quot; media=&quot;screen&quot;/&gt;
&lt;/head&gt;
&lt;body&gt;$body&lt;/body&gt;&lt;/html&gt;
EOT;
    }

    /**
     * Return select options for xml from array
     *
     * @param array $dataArray - source array
     * @param string $info - selected item
     * @return string
     */
    public function getArrayAsXmlItemValues($dataArray, $selected)
    {
        $items = array();
        foreach ($dataArray as $k => $v) {
            if (!$k) {
                continue;
            }
            $items[] = '
            <item' . ($k == $selected ? ' selected="1"' : '') . '>
                <label>' . $v . '</label>
                <value>' . ($k ? $k : '') . '</value>
            </item>';
        }
        $result = implode('', $items);
        return $result;
    }

    /**
     * Return Solo Xml optional fieldset
     *
     * @param string $ssCcMonths
     * @param string $ssCcYears
     * @return string
     */
    public function getSoloXml($ssCcMonths, $ssCcYears)
    {
        // issue number ==== validate-cc-ukss cvv
        $solo = <<<EOT
<fieldset_optional>
    <depend_on name="payment[cc_type]">
        <show_value>
            <item>SS</item>
            <item>SM</item>
            <item>SO</item>
        </show_value>
    </depend_on>

    <field name="payment[cc_ss_issue]" type="text" label="{$this->__('Issue Number')}">
        <validators>
            <validator relation="payment[cc_type]" type="credit_card_ukss" message="{$this->__('Please enter issue number or start date for switch/solo card type.')}"/>
        </validators>
    </field>;
    <field name="payment[cc_ss_start_month]" type="select" label="{$this->__('Start Date - Month')}">
        <values>
            $ssCcMonths
        </values>
    </field>
    <field name="payment[cc_ss_start_year]" type="select" label="{$this->__('Start Date - Year')}">
        <values>
            $ssCcYears
        </values>
    </field>
</fieldset_optional>
EOT;
        return $solo;
    }

    /**
     * Format price for correct view inside xml strings
     *
     * @param float $price
     * @return string
     */
    public function formatPriceForXml($price)
    {
        return sprintf('%01.2F', $price);
    }

    /**
     * Get list of predefined and supported message types
     *
     * @return array
     */
    static public function getSupportedMessageTypes()
    {
        $messages = array (
            Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_PUSH      => Mage::helper('xmlconnect')->__('Push message'),
            Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_AIRMAIL   => Mage::helper('xmlconnect')->__('AirMail message'),
        );

        return $messages;
    }

    /**
     * Retrieve supported message types as "html select options"
     *
     * @return array
     */
    public function getMessageTypeOptions()
    {
        $options = array();
        $messages = self::getSupportedMessageTypes();
        foreach ($messages as $type => $label) {
            $options[] = array('value' => $type, 'label' => $label);
        }
        return $options;
    }

    /**
     * Get push title length
     *
     * @return int
     */
    public function getPushTitleLength()
    {
        return self::PUSH_TITLE_LENGTH;
    }

    /**
     * Get message title length
     *
     * @return int
     */
    public function getMessageTitleLength()
    {
        return self::MESSAGE_TITLE_LENGTH;
    }

    /**
     * Retrieve applications as "html select options"
     *
     * @return array
     */
    public function getApplicationOptions()
    {
        $options = array();
        foreach (Mage::getModel('xmlconnect/application')->getCollection() as $app) {
            if (self::isTemplateAllowedForApplication($app)) {
                $options[] = array('value' => $app->getCode(), 'label' => $app->getName());
            }
        }
        if (count($options) > 1) {
            $options[] = array('value' => '', 'label' => Mage::helper('xmlconnect')->__('Please Select Application'));
        }
        return $options;
    }

    /**
     * Get applications array like `code` as `name`
     *
     * @param
     * @staticvar array $apps
     * @return array
     */
    public function getApplications()
    {
        static $apps = array();

        if (empty($apps)) {
            foreach (Mage::getModel('xmlconnect/application')->getCollection() as $app) {
                $apps[$app->getCode()] = $app->getName();
            }
        }
        return $apps;
    }

    /**
     * Check if creating AirMail template for the application is allowed
     *
     * @param Mage_XmlConnect_Model_Application $application
     * @return boolean
     */
    public static function isTemplateAllowedForApplication($application = null)
    {
        return $application instanceof Mage_XmlConnect_Model_Application ?
            in_array($application->getType(), array(self::DEVICE_TYPE_IPHONE)) :
            false;
    }

    /**
     * Send broadcast message
     *
     * @param Mage_XmlConnect_Model_Queue $queue
     */
    public function sendBroadcastMessage(Mage_XmlConnect_Model_Queue $queue)
    {
        if ($queue->getStatus() != Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE) {
            return;
        }

        try {
            $appCode = $queue->getAppCode();
            $app = Mage::getModel('xmlconnect/application')->load($appCode, 'code');

            if (!$app->getId()) {
                Mage::throwException(Mage::helper('xmlconnect')->__('Can\'t load application with code "%s"', $appCode));
            }

            $userpwd = $app->getUserpwd();

            $sendType = $queue->getData('type');
            switch ($sendType) {
                case Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_AIRMAIL:
                    $broadcastUrl = Mage::getStoreConfig('xmlconnect/' . Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_AIRMAIL . '/broadcast_url');
                    $params = $queue->getAirmailBroadcastParams();
                    break;

                case Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_PUSH:
                default:
                    $broadcastUrl = Mage::getStoreConfig('xmlconnect/' . Mage_XmlConnect_Model_Queue::MESSAGE_TYPE_PUSH . '/broadcast_url');
                    $params = $queue->getPushBroadcastParams();
                    break;
            }

            $ch = curl_init($broadcastUrl);

            $httpHeaders = $this->getHttpHeaders();

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            // Execute the request.
            $result = curl_exec($ch);
            $succeeded  = curl_errno($ch) == 0 ? true : false;

            // close cURL resource, and free up system resources
            curl_close($ch);

            if ($succeeded && (is_null($result) || strtolower($result) == 'null')) {
                $queue->setStatus(Mage_XmlConnect_Model_Queue::STATUS_COMPLETED);
            }
            $queue->setIsSent(true);

            return;
        } catch (Exception $e) {
            Mage::logException($e);
            throw $e;
        }
    }

    /**
     * Get headers for broadcast message
     *
     * @return string
     */
    public function getHttpHeaders()
    {
        $httpHeaders = array('Content-Type: application/json');
        return $httpHeaders;
    }

    /**
     * Remove from array the unnecessary parameters by given keys
     *
     * @param array $data Source array
     * @param array $excludedKeys Keys to be excluded from array. Keys must be in xPath notation
     * @return array
     */
    public function excludeXmlConfigKeys($data, $excludedKeys = array())
    {
        $excludedKeys = $this->getExcludedXmlConfigKeys();
        if (!empty($excludedKeys)) {
            foreach ($excludedKeys as $keyToExclude) {
                if (strpos($keyToExclude, '/')) {
                    $keys = array();
                    foreach (explode('/', $keyToExclude) as $key) {
                        $key = trim($key);
                        if (!empty($key)) {
                            $keys[] = $key;
                        }
                    }
                    if (!empty($keys)) {
                        $keys = '$data["' . implode('"]["', $keys) . '"]';
                        eval('if (isset(' . $keys . ')) unset(' . $keys . ');');
                    }
                } elseif (!empty($keyToExclude) && isset($data[$keyToExclude])) {
                    unset($data[$keyToExclude]);
                }
            }
        }
        return $data;
    }

    /**
     * Get excluded keys as array
     *
     * @return array
     */
    public function getExcludedXmlConfigKeys()
    {
        $toExclude = Mage::getStoreConfig(self::XML_NODE_CONFIG_EXCLUDE_FROM_XML);
        $nodes = array();
        foreach ($toExclude as $value) {
            $nodes[] = trim($value, '/');
        }

        return $nodes;
    }

    /**
     * Returns Application name by it's code
     * @param  string $appCode
     * @return string
     */
    public function getApplicationName($appCode = null)
    {
        if (empty($appCode)) {
            return '';
        }
        if (!isset($this->_appNames[$appCode])) {
            $app = Mage::getModel('xmlconnect/application')->loadByCode($appCode);
            if ($app->getId()) {
                $this->_appNames[$appCode] = $app->getName();
            } else {
                return '';
            }
        }
        return $this->_appNames[$appCode];
    }

    /**
     * Returns Application name by it's code
     * @param  string $appCode
     * @return string
     */
    public function getTemplateName($templateId = null)
    {
        if (empty($templateId)) {
            return '';
        }
        if (!isset($this->_tplNames[$templateId])) {
            $template = Mage::getModel('xmlconnect/template')->load($templateId);
            if ($template->getId()) {
                $this->_tplNames[$templateId] = $template->getName();
            } else {
                return '';
            }
        }
        return $this->_tplNames[$templateId];
    }

    /**
     * Set value into multidimensional array 'conf/native/navigationBar/icon'
     *
     * @param &array $target // pointer to target array
     * @param string $fieldPath // 'conf/native/navigationBar/icon'
     * @param mixed $fieldValue // 'Some Value' || 12345 || array(1=>3, 'aa'=>43)
     * @param string $delimiter // path delimiter
     * @return null
     */
    public function _injectFieldToArray(&$target, $fieldPath, $fieldValue, $delimiter = '/')
    {
        $nameParts = explode($delimiter, $fieldPath);
        foreach ($nameParts as $next) {
            if (!isset($target[$next])) {
                $target[$next] = array();
            }
            $target =& $target[$next];
        }
        $target = $fieldValue;
        return null;
    }

    /**
     * Convert Url link to file path for images
     *
     * @param string $icon
     * @return string
     */
    public function urlToPath($icon)
    {
        $baseUrl = Mage::getBaseUrl('media');
        $path = str_replace($baseUrl, '', $icon);
        $filePath = Mage::getBaseDir('media') . DS . str_replace('/', DS, $path);
        return $filePath;

    }

    /**
     * Validate config body field is not empty
     *
     * @param string $field
     * @param array $native
     * @return bool
     */
    public function validateConfFieldNotEmpty($field, $native)
    {
        if ( ($native === false)
            || (!isset($native['body']) || !is_array($native['body'])
            || !isset($native['body'][$field])
            || !Zend_Validate::is($native['body'][$field], 'NotEmpty'))) {
            return false;
        }
        return true;
    }
}
