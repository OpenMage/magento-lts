<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Persistent
 */

/**
 * Persistent Shopping Cart Data Helper
 *
 * @package    Mage_Persistent
 */
class Mage_Persistent_Helper_Data extends Mage_Core_Helper_Data
{
    public const XML_PATH_ENABLED = 'persistent/options/enabled';

    public const XML_PATH_LIFE_TIME = 'persistent/options/lifetime';

    public const XML_PATH_LOGOUT_CLEAR = 'persistent/options/logout_clear';

    public const XML_PATH_REMEMBER_ME_ENABLED = 'persistent/options/remember_enabled';

    public const XML_PATH_REMEMBER_ME_DEFAULT = 'persistent/options/remember_default';

    public const XML_PATH_PERSIST_SHOPPING_CART = 'persistent/options/shopping_cart';

    public const LOGGED_IN_LAYOUT_HANDLE = 'customer_logged_in_psc_handle';

    public const LOGGED_OUT_LAYOUT_HANDLE = 'customer_logged_out_psc_handle';

    protected $_moduleName = 'Mage_Persistent';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Checks whether Persistence Functionality is enabled
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether "Remember Me" enabled
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isRememberMeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_ENABLED, $store);
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isRememberMeCheckedDefault($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_DEFAULT, $store);
    }

    /**
     * Is shopping cart persist
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function isShoppingCartPersist($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PERSIST_SHOPPING_CART, $store);
    }

    /**
     * Get Persistence Lifetime
     *
     * @param  int|Mage_Core_Model_Store|string $store
     * @return int
     */
    public function getLifeTime($store = null)
    {
        $lifeTime = Mage::getStoreConfigAsInt(self::XML_PATH_LIFE_TIME, $store);
        return max(0, $lifeTime);
    }

    /**
     * Check if set `Clear on Logout` in config settings
     *
     * @return bool
     */
    public function getClearOnLogout()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LOGOUT_CLEAR);
    }

    /**
     * Retrieve url for unset long-term cookie
     *
     * @return string
     */
    public function getUnsetCookieUrl()
    {
        return $this->_getUrl('persistent/index/unsetCookie');
    }

    /**
     * Retrieve name of persistent customer
     *
     * @return string
     */
    public function getPersistentName()
    {
        return $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether specified action should be processed
     *
     * @param  Varien_Event_Observer $observer
     * @return bool
     */
    public function canProcess($observer)
    {
        $action = $observer->getEvent()->getAction();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($action instanceof Mage_Core_Controller_Varien_Action) {
            return !$action->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }

        if ($controllerAction instanceof Mage_Core_Controller_Varien_Action) {
            return !$controllerAction->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }

        return true;
    }

    /**
     * Get create account url depends on checkout
     *
     * @param  string $url
     * @return string
     */
    public function getCreateAccountUrl($url)
    {
        if (Mage::helper('checkout')->isContextCheckout()) {
            return Mage::helper('core/url')->addRequestParam($url, ['context' => 'checkout']);
        }

        return $url;
    }
}
