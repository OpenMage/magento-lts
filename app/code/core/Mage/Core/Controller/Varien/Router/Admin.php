<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 */
class Mage_Core_Controller_Varien_Router_Admin extends Mage_Core_Controller_Varien_Router_Standard
{
    /**
     * Fetch default path
     */
    public function fetchDefault()
    {
        // set defaults
        $d = explode('/', $this->_getDefaultPath());
        $this->getFront()->setDefault([
            'module'     => !empty($d[0]) ? $d[0] : '',
            'controller' => !empty($d[1]) ? $d[1] : 'index',
            'action'     => !empty($d[2]) ? $d[2] : 'index',
        ]);
    }

    /**
     * Get router default request path
     * @return string
     */
    protected function _getDefaultPath()
    {
        return (string) Mage::getConfig()->getNode('default/web/default/admin');
    }

    /**
     * Validate admin domain before routing
     *
     * @return bool
     */
    protected function _beforeModuleMatch()
    {
        // Check if custom admin domain is configured
        if ($adminUrl = Mage_Adminhtml_Helper_Data::getCustomAdminUrl()) {
            $adminHost = parse_url($adminUrl, PHP_URL_HOST);
            if (!$adminHost) {
                // Should never happen - URL is validated when saved
                // If it does, fail secure (possible database corruption/bypass)
                Mage::log(
                    "Unable to parse custom admin URL host: {$adminUrl}. Access denied for security.",
                    Zend_Log::ERR,
                    'system.log',
                );
                return false;
            }

            $currentHost = $this->getFront()->getRequest()->getHttpHost();
            // Strip port for comparison (getHttpHost may include port)
            $currentHost = preg_replace('/:\d+$/', '', $currentHost);

            return strtolower($adminHost) === strtolower($currentHost);
        }

        return true;
    }

    /**
     * checking if we installed or not and doing redirect
     *
     * @return bool
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    protected function _afterModuleMatch()
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        return true;
    }

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @return bool
     */
    protected function _noRouteShouldBeApplied()
    {
        return true;
    }

    /**
     * Check whether URL for corresponding path should use https protocol
     *
     * @param string $path
     * @return bool
     */
    protected function _shouldBeSecure($path)
    {
        return str_starts_with((string) Mage::getConfig()->getNode('default/web/unsecure/base_url'), 'https')
            || Mage::getStoreConfigFlag(Mage_Core_Model_Store::XML_PATH_SECURE_IN_ADMINHTML, Mage_Core_Model_App::ADMIN_STORE_ID)
                && str_starts_with((string) Mage::getConfig()->getNode('default/web/secure/base_url'), 'https');
    }

    /**
     * Retrieve current secure url
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return string
     */
    protected function _getCurrentSecureUrl($request)
    {
        return Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->getBaseUrl('link', true) . ltrim($request->getPathInfo(), '/');
    }

    /**
     * Emulate custom admin url
     *
     * @param string $configArea
     * @param bool $useRouterName
     */
    public function collectRoutes($configArea, $useRouterName)
    {
        if ((string) Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH)) {
            $customUrl = (string) Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_CUSTOM_ADMIN_PATH);
            $xmlPath = Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME;
            if ((string) Mage::getConfig()->getNode($xmlPath) != $customUrl) {
                Mage::getConfig()->setNode($xmlPath, $customUrl, true);
            }
        }

        parent::collectRoutes($configArea, $useRouterName);
    }

    /**
     * Add module definition to routes.
     *
     * @inheritDoc
     */
    public function addModule($frontName, $moduleName, $routeName)
    {
        $isExtensionsCompatibilityMode = (bool) (string) Mage::getConfig()->getNode(
            'default/admin/security/extensions_compatibility_mode',
        );
        $configRouterFrontName = (string) Mage::getConfig()->getNode(
            Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME,
        );
        if ($isExtensionsCompatibilityMode || ($frontName == $configRouterFrontName)) {
            return parent::addModule($frontName, $moduleName, $routeName);
        } else {
            return $this;
        }
    }

    /**
     * Check if current controller instance is allowed in current router.
     *
     * @param Mage_Core_Controller_Varien_Action $controllerInstance
     * @return true
     */
    protected function _validateControllerInstance($controllerInstance)
    {
        return true;
    }
}
