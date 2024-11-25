<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
