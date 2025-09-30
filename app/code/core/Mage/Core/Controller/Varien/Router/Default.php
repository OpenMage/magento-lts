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
class Mage_Core_Controller_Varien_Router_Default extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Modify request and set to no-route action
     * If store is admin and specified different admin front name,
     * change store to default (Possible when enabled Store Code in URL)
     *
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $noRoute        = explode('/', $this->_getNoRouteConfig());
        $moduleName     = isset($noRoute[0]) && $noRoute[0] ? $noRoute[0] : 'core';
        $controllerName = isset($noRoute[1]) && $noRoute[1] ? $noRoute[1] : 'index';
        $actionName     = isset($noRoute[2]) && $noRoute[2] ? $noRoute[2] : 'index';

        if ($this->_isAdmin()) {
            $adminFrontName = (string) Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
            if ($adminFrontName != $moduleName) {
                $moduleName     = 'core';
                $controllerName = 'index';
                $actionName     = 'noRoute';
                Mage::app()->setCurrentStore(Mage::app()->getDefaultStoreView());
            }
        }

        $request->setModuleName($moduleName)
            ->setControllerName($controllerName)
            ->setActionName($actionName);

        return true;
    }

    /**
     * Retrieve default router config
     *
     * @return string
     */
    protected function _getNoRouteConfig()
    {
        return Mage::app()->getStore()->getConfig('web/default/no_route');
    }

    /**
     * Check if store is admin store
     *
     * @return bool
     */
    protected function _isAdmin()
    {
        return Mage::app()->getStore()->isAdmin();
    }
}
