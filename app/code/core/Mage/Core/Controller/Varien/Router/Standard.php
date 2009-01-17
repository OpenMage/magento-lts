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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_modules = array();
    protected $_routes = array();
    protected $_dispatchData = array();

    public function collectRoutes($configArea, $useRouterName)
    {
        $routers = array();
        $routersConfigNode = Mage::getConfig()->getNode($configArea.'/routers');
        if($routersConfigNode) {
            $routers = $routersConfigNode->children();
        }
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            if ($use==$useRouterName) {
                $moduleName = (string)$routerConfig->args->module;
                $frontName = (string)$routerConfig->args->frontName;
                $this->addModule($frontName, $moduleName, $routerName);
            }
        }
    }

    public function fetchDefault()
    {
        $d = explode('/', Mage::getStoreConfig('web/default/front'));
        $this->getFront()->setDefault(array(
            'module'     => !empty($d[0]) ? $d[0] : 'core',
            'controller' => !empty($d[1]) ? $d[1] : 'index',
            'action'     => !empty($d[2]) ? $d[2] : 'index'
        ));
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        $this->fetchDefault();

        $front = $this->getFront();

        $p = explode('/', trim($request->getPathInfo(), '/'));

        // get module name
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
            if(!empty($p[0])) {
            	$module = $p[0];
            } else {
            	$module = $this->getFront()->getDefault('module');
                $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,	'');
            }
        }
        if (!$module) {
            return false;
        }
        $realModule = $this->getModuleByFrontName($module);
        if (!$realModule) {
            if ($moduleFrontName = array_search($module, $this->_modules)) {
                $realModule = $module;
                $module = $moduleFrontName;
            } else {
                return false;
            }
        }

        $request->setRouteName($this->getRouteByFrontName($module));

        // get controller name
        if ($request->getControllerName()) {
            $controller = $request->getControllerName();
        } else {
            if (!empty($p[1])) {
                $controller = $p[1];
            } else {
            	$controller = $front->getDefault('controller');
            	$request->setAlias(
            	   Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            	   ltrim($request->getOriginalPathInfo(), '/')
            	);
            }
        }
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

        // get action name
        if (empty($action)) {
            if ($request->getActionName()) {
                $action = $request->getActionName();
            } else {
                $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
            }
        }

        $this->_checkShouldBeSecure($request, '/'.$module.'/'.$controller.'/'.$action);

        // include controller file if needed
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist'));
            }
        }

        // instantiate controller class
        $controllerInstance = new $controllerClassName($request, $front->getResponse());

        if (!$controllerInstance->hasAction($action)) {
            return false;
        }

        // set values only after all the checks are done
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);

        // set parameters from pathinfo
        for ($i=3, $l=sizeof($p); $i<$l; $i+=2) {
            $request->setParam($p[$i], isset($p[$i+1]) ? $p[$i+1] : '');
        }

        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);

        return true;#$request->isDispatched();
    }

    public function addModule($frontName, $moduleName, $routeName)
    {
        $this->_modules[$frontName] = $moduleName;
        $this->_routes[$routeName] = $frontName;
        return $this;
    }

    public function getModuleByFrontName($frontName)
    {
        if (isset($this->_modules[$frontName])) {
            return $this->_modules[$frontName];
        }
        return false;
    }

    public function getFrontNameByRoute($routeName)
    {
        if (isset($this->_routes[$routeName])) {
            return $this->_routes[$routeName];
        }
        return false;
    }

    public function getRouteByFrontName($frontName)
    {
        return array_search($frontName, $this->_routes);
    }

    public function getControllerFileName($realModule, $controller)
    {
        $file = Mage::getModuleDir('controllers', $realModule);
        $file .= DS.uc_words($controller, DS).'Controller.php';
        return $file;
    }

    public function validateControllerFileName($fileName)
    {
        if ($fileName && is_readable($fileName) && false===strpos($fileName, '//')) {
            return true;
        }
        return false;
    }

    public function getControllerClassName($realModule, $controller)
    {
        $class = $realModule.'_'.uc_words($controller).'Controller';
        return $class;
    }

    public function rewrite(array $p)
    {
        $rewrite = Mage::getConfig()->getNode('global/rewrite');
        if ($module = $rewrite->{$p[0]}) {
            if (!$module->children()) {
                $p[0] = trim((string)$module);
            }
        }
        if (isset($p[1]) && ($controller = $rewrite->{$p[0]}->{$p[1]})) {
            if (!$controller->children()) {
                $p[1] = trim((string)$controller);
            }
        }
        if (isset($p[2]) && ($action = $rewrite->{$p[0]}->{$p[1]}->{$p[2]})) {
            if (!$action->children()) {
                $p[2] = trim((string)$action);
            }
        }
#echo "<pre>".print_r($p,1)."</pre>";
        return $p;
    }

    protected function _checkShouldBeSecure($request, $path='')
    {
        if (!Mage::isInstalled() || $request->getPost()) {
            return;
        }

        if ($this->_shouldBeSecure($path) && !Mage::app()->getStore()->isCurrentlySecure()) {
            $url = $this->_getCurrentSecureUrl($request);

            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($url)
                ->sendResponse();
            exit;
        }
    }

    protected function _getCurrentSecureUrl($request)
    {
        return Mage::getBaseUrl('link', true).ltrim($request->getPathInfo(), '/');
    }

    protected function _shouldBeSecure($path)
    {
        return substr(Mage::getStoreConfig('web/unsecure/base_url'),0,5)==='https'
            || Mage::getStoreConfigFlag('web/secure/use_in_frontend')
            && substr(Mage::getStoreConfig('web/secure/base_url'),0,5)=='https'
            && Mage::getConfig()->shouldUrlBeSecure($path);
    }
}