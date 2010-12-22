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


/**
 * @method Mage_XmlConnect_Model_Mysql4_Application _getResource()
 * @method Mage_XmlConnect_Model_Mysql4_Application getResource()
 * @method string getName()
 * @method Mage_XmlConnect_Model_Application setName(string $value)
 * @method string getCode()
 * @method Mage_XmlConnect_Model_Application setCode(string $value)
 * @method string getType()
 * @method Mage_XmlConnect_Model_Application setType(string $value)
 * @method Mage_XmlConnect_Model_Application setStoreId(int $value)
 * @method string getActiveFrom()
 * @method Mage_XmlConnect_Model_Application setActiveFrom(string $value)
 * @method string getActiveTo()
 * @method Mage_XmlConnect_Model_Application setActiveTo(string $value)
 * @method string getUpdatedAt()
 * @method Mage_XmlConnect_Model_Application setUpdatedAt(string $value)
 * @method string getConfiguration()
 * @method Mage_XmlConnect_Model_Application setConfiguration(string $value)
 * @method int getStatus()
 * @method Mage_XmlConnect_Model_Application setStatus(int $value)
 * @method int getBrowsingMode()
 * @method Mage_XmlConnect_Model_Application setBrowsingMode(int $value)
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Application extends Mage_Core_Model_Abstract
{

    /**
     * Application code cookie name
     */
    const APP_CODE_COOKIE_NAME = 'app_code';

    /**
     * Device screen size name
     */
    const APP_SCREEN_SIZE_NAME = 'screen_size';


    /**
     * Device screen size name
     */
    const APP_SCREEN_SIZE_DEFAULT = '320x480';

    /**
     * Device screen size source name
     */
    const APP_SCREEN_SOURCE_DEFAULT = 'default';

    /**
     * Application status "submitted" value
     *
     * @var int
     */
    const APP_STATUS_SUCCESS = 1;

    /**
     * Application status "not submitted" value
     *
     * @var int
     */
    const APP_STATUS_INACTIVE = 0;

    /**
     * Application prefix length of cutted part of deviceType and storeCode
     *
     * @var int
     */
    const APP_PREFIX_CUT_LENGTH = 3;

    /**
     * Images in "Params" history table
     *
     * @var array
     */
    protected $_imageIds = array('icon', 'loader_image', 'logo', 'big_logo');


    /**
     * Initialize application
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/application');
    }

    /**
     * Checks is it app is submitted
     * (edit is premitted only before submission)
     *
     * @return bool
     */
    public function getIsSubmitted()
    {
        return $this->getStatus() == Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS;
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @return array
     */
    public function getFormData()
    {
        $data = $this->getData();
        return $this->_flatArray($data);
    }

    /**
     * Load data (flat array) for Varien_Data_Form
     *
     * @param array $subtree
     * @param string $prefix
     * @return array
     */
    protected function _flatArray($subtree, $prefix=null)
    {
        $result = array();
        foreach ($subtree as $key => $value) {
            if (is_null($prefix)) {
                $name = $key;
            } else {
                $name = $prefix . '[' . $key . ']';
            }

            if (is_array($value)) {
                $result = array_merge($result, $this->_flatArray($value, $name));
            } else {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * Like array_merge_recursive(), but string values is replaced
     *
     * @param array $a
     * @param array $b
     * @return array
     */
    protected function _configMerge (array $a, array $b)
    {
        $result = array();
        $keys = array_unique(array_merge(array_keys($a), array_keys($b)));
        foreach ($keys as $key) {
            if (!isset($a[$key])) {
                $result[$key] = $b[$key];
            } elseif (!isset($b[$key])) {
                $result[$key] = $a[$key];
            } elseif (is_scalar($a[$key]) || is_scalar($b[$key])) {
                $result[$key] = $b[$key];
            } else {
                $result[$key] = $this->_configMerge($a[$key], $b[$key]);
            }
        }
        return $result;
    }

    /**
     * Set default configuration data
     */
    public function loadDefaultConfiguration()
    {
        $this->setType('iphone');
        $this->setCode($this->getCodePrefix());
        $this->setConf(Mage::helper('xmlconnect/iphone')->getDefaultConfiguration());
    }

    /**
     * Return first part for application code field
     *
     * @return string
     */
    public function getCodePrefix()
    {
        return substr(Mage::app()->getStore($this->getStoreId())->getCode(), 0, self::APP_PREFIX_CUT_LENGTH)
            . substr($this->getType(), 0, self::APP_PREFIX_CUT_LENGTH);
    }

    /**
     * Checks if application code field has autoincrement
     *
     * @return bool
     */
    public function isCodePrefixed()
    {
        $suffix = substr($this->getCode(), self::APP_PREFIX_CUT_LENGTH * 2);
        return !empty($suffix);
    }

    /**
     * Load application configuration
     *
     * @return array
     */
    public function prepareConfiguration()
    {
        return $this->getData('conf');
    }

    /**
     * Get config formatted for rendering
     *
     * @return array
     */
    public function getRenderConf()
    {
        $result = Mage::helper('xmlconnect/iphone')->getDefaultConfiguration();
        $result = $result['native'];
        $extra = array();
        if (isset($this->_data['conf'])) {
            if (isset($this->_data['conf']['native'])) {
                $result = $this->_configMerge($result, $this->_data['conf']['native']);
            }
            if (isset($this->_data['conf']['extra'])) {
                $extra = $this->_data['conf']['extra'];
                if (isset($extra['tabs'])) {
                    $tabs = Mage::getModel('xmlconnect/tabs', $extra['tabs']);
                    $result['tabBar']['tabs'] = $tabs;
                }
                if (isset($extra['fontColors'])) {
                    if (!empty($extra['fontColors']['header'])) {
                        $result['fonts']['Title1']['color'] = $extra['fontColors']['header'];
                    }
                    if (!empty($extra['fontColors']['primary'])) {
                        $result['fonts']['Title2']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Title3']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Text1']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Text2']['color'] = $extra['fontColors']['primary'];
                        $result['fonts']['Title7']['color'] = $extra['fontColors']['primary'];
                    }
                    if (!empty($extra['fontColors']['secondary'])) {
                        $result['fonts']['Title4']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title6']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title8']['color'] = $extra['fontColors']['secondary'];
                        $result['fonts']['Title9']['color'] = $extra['fontColors']['secondary'];
                    }
                    if (!empty($extra['fontColors']['price'])) {
                        $result['fonts']['Title5']['color'] = $extra['fontColors']['price'];
                    }
                }
            }
        }
        $helperImage = Mage::helper('xmlconnect/image');
        $screenSize = $this->getScreenSize();
        $paths = $helperImage->getInterfaceImagesPathsConf();
        foreach ($paths as $confPath => $dataPath) {
            $imageNodeValue =& $helperImage->findPath($result, $dataPath);
            if ($imageNodeValue) {
                /**
                 * Creating file ending (some_inner/some_dir/filename.png) For url
                 */
                $imageNodeValue = $helperImage->getFileCustomDirSuffixAsUrl($confPath, $imageNodeValue);
            }
        }
        $result = $this->_absPath($result);
        /**
         * General configuration
         */
        $result['general']['updateTimeUTC'] = strtotime($this->getUpdatedAt());
        $result['general']['browsingMode'] = $this->getBrowsingMode();
        $result['general']['currencyCode'] = Mage::app()->getStore($this->getStoreId())->getDefaultCurrencyCode();
        $result['general']['secureBaseUrl'] = Mage::getStoreConfig('web/secure/base_url', $this->getStoreId());
        $maxRecipients = 0;
        $allowGuest = 0;
        if (Mage::getStoreConfig('sendfriend/email/enabled')) {
            $maxRecipients = Mage::getStoreConfig('sendfriend/email/max_recipients');
            $allowGuest = Mage::getStoreConfig('sendfriend/email/allow_guest');
        }
        $result['general']['emailToFriendMaxRecepients'] = $maxRecipients;
        $result['general']['emailAllowGuest'] = $allowGuest;
        $result['general']['primaryStoreLang'] = Mage::app()
            ->getStore($this->getStoreId())->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        $result['general']['magentoVersion'] = Mage::getVersion();
        $result['general']['copyright'] = Mage::getStoreConfig('design/footer/copyright', $this->getStoreId());

        $result['general']['isAllowedGuestCheckout'] = Mage::getSingleton('checkout/session')
            ->getQuote()->isAllowedGuestCheckout();

        /**
         * PayPal configuration
         */
        $result['paypal']['businessAccount'] = Mage::getModel('paypal/config')->businessAccount;
        $result['paypal']['merchantLabel'] = $this->getData('conf/special/merchantLabel');

        $isActive = 0;
        if (isset($result['paypal']) && isset($result['paypal']['isActive'])) {
            $isActive = (int)($result['paypal']['isActive'] && Mage::getModel('xmlconnect/payment_method_paypal_mep')->isAvailable(null, $this->getStoreId()));
        }
        $result['paypal']['isActive'] = $isActive;

        return $result;
    }

    /**
     * Return current screen_size parameter
     *
     * @return string
     */
    public function getScreenSize()
    {
        if (!isset($this->_data['screen_size'])) {
            $this->_data['screen_size'] = self::APP_SCREEN_SIZE_DEFAULT;
        }
        return $this->_data['screen_size'];
    }

    /**
     * Setter
     * for current screen_size parameter
     *
     * @param string $screenSize
     * @return this
     */
    public function setScreenSize($screenSize)
    {
        $this->_data['screen_size'] = Mage::helper('xmlconnect/image')->filterScreenSize((string) $screenSize);
        return $this;
    }

    /**
     * Return Enabled Tabs array from actual config
     *
     * @return array:
     */
    public function getEnabledTabsArray()
    {
        if ($this->getData('conf/extra/tabs')) {
            return Mage::getModel('xmlconnect/tabs', $this->getData('conf/extra/tabs'))->getRenderTabs();
        }
        return array();
    }

    /**
     * Change URLs to absolute
     *
     * @param array $subtree
     * @return array
     */
    protected function _absPath($subtree)
    {
        foreach ($subtree as $key => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    $subtree[$key] = $this->_absPath($value);
                } elseif ((substr($key, -4) == 'icon') ||
                    (substr($key, -4) == 'Icon') ||
                    (substr($key, -5) == 'Image')) {
                    $subtree[$key] = Mage::getBaseUrl('media') . 'xmlconnect/' . $value;
                }
            }
        }
        return $subtree;
    }

    /**
     * Return content pages
     *
     * @return array
     */
    public function getPages()
    {
        if (isset($this->_data['conf']['native']['pages'])) {
            return $this->_data['conf']['native']['pages'];
        }
        return array();
    }

    /**
     * Processing object before save data
     *
     * @return Mage_XmlConnect_Model_Application
     */
    protected function _beforeSave()
    {
        $conf = serialize($this->prepareConfiguration());
        $this->setConfiguration($conf);
        $this->setUpdatedAt(date('Y-m-d H:i:s', time()));
        return $this;
    }

    /**
     * Load configuration data (from serialized blob)
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function loadConfiguration()
    {
        $configuration = $this->getConfiguration();
        if (!empty($configuration)) {
            $configuration = unserialize($configuration);
            $this->setData('conf', $configuration);
        }
        return $this;
    }

    /**
     * Load application by code
     *
     * @param string $code
     * @return Mage_XmlConnect_Model_Application
     */
    public function loadByCode($code)
    {
        $this->_getResource()->load($this, $code, 'code');
        return $this;
    }

    /**
     * Loads submit tab data from xmlconnect/history table
     *
     * @return bool
     */
    public function loadSubmit()
    {
        $isResubmitAction = false;
        if ($this->getId()) {
            $params = $this->getLastParams();
            if (!empty($params)) {
                // Using Pointer !
                $conf = &$this->_data['conf'];
                if (!isset($conf['submit_text']) || !is_array($conf['submit_text'])) {
                    $conf['submit_text'] = array();
                }
                if (!isset($conf['submit_restore']) || !is_array($conf['submit_restore'])) {
                    $conf['submit_restore'] = array();
                }
                foreach ($params as $id => $value) {
                    if (!in_array($id, $this->_imageIds)) {
                        $conf['submit_text'][$id] = $value;
                    } else {
                        $conf['submit_restore'][$id] = $value;
                    }
                    $isResubmitAction = true;
                }
            }
        }
        $this->setIsResubmitAction($isResubmitAction);
        return $isResubmitAction;
    }

    /**
     * Returns ( image[ ID ] => "SRC" )  array
     *
     * @return array
     */
    public function getImages()
    {
        $images = array();
        $params = $this->getLastParams();

        foreach ($this->_imageIds as $id) {
            $path = $this->getData('conf/submit/'.$id);
            $basename = null;
            if (!empty($path)) {
                /**
                 * Fetching data from session restored array
                 */
                 $basename = basename($path);
            } else if (isset($params[$id])) {
               /**
                * Fetching data from submission history table record
                *
                * converting :  "@\var\somedir\media\xmlconnect\form_icon_6.png" to "\var\somedir\media\xmlconnect\forn_icon_6.png"
                */
//                $path = substr($params[$id], 1);
                $basename = basename($params[$id]);
            }
            if (!empty($basename)) {
                $images['conf/submit/'.$id] = Mage::getBaseUrl('media').'xmlconnect/'
                    . Mage::helper('xmlconnect/image')->getFileDefaultSizeSuffixAsUrl($basename);
            }
        }
        return $images;
    }

    /**
     * Return last submitted data from history table
     *
     * @return array
     */
    public function getLastParams()
    {
        if (!isset($this->_lastParams)) {
            $this->_lastParams = Mage::getModel('xmlconnect/history')->getLastParams($this->getId());
        }
        return $this->_lastParams;
    }

    /**
     * Validate application data
     *
     * @return array|bool
     */
    public function validate()
    {
        $errors = array();

        $validateConf = $this->_validateConf();
        if ($validateConf !== true) {
            $errors = $validateConf;
        }
        if (!Zend_Validate::is($this->getName(), 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter "App Title".');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Validate submit application data
     *
     * @param array $params
     * @return array|bool
     */
    public function validateSubmit($params)
    {
        $errors = array();
        $validateConf = $this->_validateConf();
        if ($validateConf !== true) {
            $errors = $validateConf;
        }
        if (!Zend_Validate::is(isset($params['title']) ? $params['title'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter the Title.');
        }

        if (!Zend_Validate::is(isset($params['copyright']) ? $params['copyright'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please enter the Copyright.');
        }

        if (empty($params['price_free'])) {
            if (!Zend_Validate::is(isset($params['price']) ? $params['price'] : null, 'NotEmpty')) {
                $errors[] = Mage::helper('xmlconnect')->__('Please enter the Price.');
            }
        }

        if (!Zend_Validate::is(isset($params['country']) ? $params['country'] : null, 'NotEmpty')) {
            $errors[] = Mage::helper('xmlconnect')->__('Please select at least one country.');
        }

        if ($this->getIsResubmitAction()) {
            if (!Zend_Validate::is(
                    isset($params['resubmission_activation_key']) ? $params['resubmission_activation_key'] : null,
                    'NotEmpty')) {
                $errors[] = Mage::helper('xmlconnect')->__('Please enter the Resubmission Key.');
            }
        } else {
            if (!Zend_Validate::is(isset($params['key']) ? $params['key'] : null, 'NotEmpty')) {
                    $errors[] = Mage::helper('xmlconnect')->__('Please enter the Activation Key.');
            }
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Check config for valid values
     *
     * @return bool|array
     */
    protected function _validateConf()
    {
        $errors = array();
        $conf = $this->getConf();
        $native = isset($conf['native']) && is_array($conf['native']) ? $conf['native'] : false;

        if ( ($native === false)
            || (!isset($native['navigationBar']) || !is_array($native['navigationBar'])
            || !isset($native['navigationBar']['icon'])
            || !Zend_Validate::is($native['navigationBar']['icon'], 'NotEmpty'))) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "Logo in Header" field from Design Tab.');
        }

        if ( ($native === false)
            || (!isset($native['body']) || !is_array($native['body'])
            || !isset($native['body']['bannerImage'])
            || !Zend_Validate::is($native['body']['bannerImage'], 'NotEmpty'))) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "Banner on Home Screen" field from Design Tab.');
        }

        if (($native === false)
            || (!isset($native['body']) || !is_array($native['body'])
            || !isset($native['body']['backgroundImage'])
            || !Zend_Validate::is($native['body']['backgroundImage'], 'NotEmpty'))) {
            $errors[] = Mage::helper('xmlconnect')->__('Please upload  an image for "App Background" field from Design Tab.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Imports post/get data into the model
     *
     * @param array $data - $_REQUEST[]
     * @return array
     */
    public function prepareSubmitParams($data)
    {

        $params = array();
        if (isset($data['conf']) && is_array($data['conf'])) {

            if (isset($data['conf']['submit_text']) && is_array($data['conf']['submit_text'])) {
                $params = $data['conf']['submit_text'];
            }

            $params['name'] = $this->getName();
            $params['code'] = $this->getCode();
            $params['type'] = $this->getType();
            $params['url'] = Mage::getBaseUrl() . 'xmlconnect/configuration/index/app_code/' . $this->getCode();
            $params['magentoversion'] = Mage::getVersion();

            if (isset($params['country']) && is_array($params['country'])) {
                $params['country'] = implode(',', $params['country']);
            }
            if ($this->getIsResubmitAction()) {
                if (isset($params['resubmission_activation_key'])) {
                    $params['resubmission_activation_key'] = trim($params['resubmission_activation_key']);
                    $params['key'] = $params['resubmission_activation_key'];
                } else {
                    $params['key'] = '';
                }
            } else {
                $params['key'] = isset($params['key']) ? trim($params['key']) : '';
            }
            // processing files :
            $submit = array();
            if (isset($this->_data['conf']['submit']) && is_array($this->_data['conf']['submit'])) {
                 $submit = $this->_data['conf']['submit'];
            }

            $submitRestore  = array();
            if (isset($this->_data['conf']['submit_restore']) && is_array($this->_data['conf']['submit_restore'])) {
                $submitRestore = $this->_data['conf']['submit_restore'];
            }

            foreach ($this->_imageIds as $id) {
                if (isset($submit[$id])) {
                    $params[$id] = '@' . $submit[$id];
                } else if (isset($submitRestore[$id])) {
                    $params[$id] = $submitRestore[$id];
                }
            }
        }
        $this->setSubmitParams($params);
        return $params;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }

    /**
     * Getter, returns activation key for current application
     *
     * @return string|null
     */
    public function getActivationKey()
    {
        $key = null;
        if (isset($this->_data['conf']) && is_array($this->_data['conf']) &&
            isset($this->_data['conf']['submit_text']) && is_array($this->_data['conf']['submit_text']) &&
            isset($this->_data['conf']['submit_text']['key'])) {

            $key = $this->_data['conf']['submit_text']['key'];
        }
        return $key;
    }

    /**
     * Perform update for all applications "updated at" parameter with current date
     *
     * @return Mage_XmlConnect_Model_Application
     */
    public function updateAllAppsUpdatedAtParameter()
    {
        $this->_getResource()->updateAllAppsUpdatedAtParameter();
        return $this;
    }
}
