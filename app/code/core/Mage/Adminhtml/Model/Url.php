<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 *
 * @method bool getNoSecret()
 * @method $this setNoSecret(bool $value)
 */
class Mage_Adminhtml_Model_Url extends Mage_Core_Model_Url
{
    /**
     * Secret key query param name
     */
    public const SECRET_KEY_PARAM_NAME = 'key';

    /**
     * Retrieve is secure mode for ULR logic
     *
     * @return bool
     */
    public function getSecure()
    {
        if ($this->hasData('secure_is_forced')) {
            return $this->getData('secure');
        }

        return Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML);
    }

    /**
     * Force strip secret key param if _nosecret param specified
     *
     * @return Mage_Core_Model_Url
     */
    public function setRouteParams(array $data, $unsetOldParams = true)
    {
        if (isset($data['_nosecret'])) {
            $this->setNoSecret(true);
            unset($data['_nosecret']);
        } else {
            $this->setNoSecret(false);
        }

        return parent::setRouteParams($data, $unsetOldParams);
    }

    /**
     * Custom logic to retrieve Urls
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $cacheSecretKey = false;
        if (is_array($routeParams) && isset($routeParams['_cache_secret_key'])) {
            unset($routeParams['_cache_secret_key']);
            $cacheSecretKey = true;
        }

        $result = parent::getUrl($routePath, $routeParams);
        if (!$this->useSecretKey()) {
            return $result;
        }

        $route = $this->getRouteName() ? $this->getRouteName() : '*';
        $controller = $this->getControllerName() ? $this->getControllerName() : $this->getDefaultControllerName();
        $action = $this->getActionName() ? $this->getActionName() : $this->getDefaultActionName();

        if ($cacheSecretKey) {
            $secret = [self::SECRET_KEY_PARAM_NAME => "\${$controller}/{$action}\$"];
        } else {
            $secret = [self::SECRET_KEY_PARAM_NAME => $this->getSecretKey($controller, $action)];
        }

        if (is_array($routeParams)) {
            $routeParams = array_merge($secret, $routeParams);
        } else {
            $routeParams = $secret;
        }

        if (is_array($this->getRouteParams())) {
            $routeParams = array_merge($this->getRouteParams(), $routeParams);
        }

        return parent::getUrl("{$route}/{$controller}/{$action}", $routeParams);
    }

    /**
     * Generate secret key for controller and action based on form key
     *
     * @param string $controller Controller name
     * @param string $action Action name
     * @return string
     */
    public function getSecretKey($controller = null, $action = null)
    {
        $salt = Mage::getSingleton('core/session')->getFormKey();

        $p = explode('/', trim($this->getRequest()->getOriginalPathInfo(), '/'));
        if (!$controller) {
            $controller = !empty($p[1]) ? $p[1] : $this->getRequest()->getControllerName();
        }

        if (!$action) {
            $action = !empty($p[2]) ? $p[2] : $this->getRequest()->getActionName();
        }

        $secret = $controller . $action . $salt;
        return Mage::helper('core')->getHash($secret);
    }

    /**
     * Return secret key settings flag
     *
     * @return bool
     */
    public function useSecretKey()
    {
        return Mage::getStoreConfigFlag('admin/security/use_form_key') && !$this->getNoSecret();
    }

    /**
     * Enable secret key using
     *
     * @return $this
     */
    public function turnOnSecretKey()
    {
        $this->setNoSecret(false);
        return $this;
    }

    /**
     * Disable secret key using
     *
     * @return $this
     */
    public function turnOffSecretKey()
    {
        $this->setNoSecret(true);
        return $this;
    }

    /**
     * Refresh admin menu cache etc.
     */
    public function renewSecretUrls()
    {
        Mage::app()->cleanCache([Mage_Adminhtml_Block_Page_Menu::CACHE_TAGS]);
    }
}
