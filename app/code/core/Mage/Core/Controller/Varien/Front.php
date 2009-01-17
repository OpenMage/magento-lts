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


class Mage_Core_Controller_Varien_Front extends Varien_Object
{
    protected $_defaults = array();

    /**
     * Available routers array
     *
     * @var array
     */
    protected $_routers = array();

    protected $_urlCache = array();

    const XML_STORE_ROUTERS_PATH = 'web/routers';

    public function setDefault($key, $value=null)
    {
        if (is_array($key)) {
            $this->_defaults = $key;
        } else {
            $this->_defaults[$key] = $value;
        }
        return $this;
    }

    public function getDefault($key=null)
    {
        if (is_null($key)) {
            return $this->_defaults;
        } elseif (isset($this->_defaults[$key])) {
            return $this->_defaults[$key];
        }
        return false;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Adding new router
     *
     * @param   string $name
     * @param   Mage_Core_Controller_Varien_Router_Abstract $router
     * @return  Mage_Core_Controller_Varien_Front
     */
    public function addRouter($name, Mage_Core_Controller_Varien_Router_Abstract $router)
    {
        $router->setFront($this);
        $this->_routers[$name] = $router;
        return $this;
    }

    /**
     * Retrieve router by name
     *
     * @param   string $name
     * @return  Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouter($name)
    {
        if (isset($this->_routers[$name])) {
            return $this->_routers[$name];
        }
        return false;
    }

    public function init()
    {
        Mage::dispatchEvent('controller_front_init_before', array('front'=>$this));

        Varien_Profiler::start('ctrl/init');

        $routersInfo = Mage::app()->getStore()->getConfig(self::XML_STORE_ROUTERS_PATH);

        foreach ($routersInfo as $routerCode => $routerInfo) {
            if (isset($routerInfo['disabled']) && $routerInfo['disabled']) {
            	continue;
            }
            if (isset($routerInfo['class'])) {
            	$router = new $routerInfo['class'];
            	if (isset($routerInfo['area'])) {
            		$router->collectRoutes($routerInfo['area'], $routerCode);
            	}
            	$this->addRouter($routerCode, $router);
            }
        }
        Mage::dispatchEvent('controller_front_init_routers', array('front'=>$this));

        // Add default router at the last
        $default = new Mage_Core_Controller_Varien_Router_Default();
        $this->addRouter('default', $default);

//         init admin modules router
//        $admin = new Mage_Core_Controller_Varien_Router_Admin();
//        $admin->collectRoutes('admin', 'admin');
//        $this->addRouter('admin', $admin);
//
//         init standard frontend modules router
//        $standard = new Mage_Core_Controller_Varien_Router_Standard();
//        $standard->collectRoutes('frontend', 'standard');
//        $this->addRouter('standard', $standard);
//
//         init custom routers
//        Mage::dispatchEvent('controller_front_init_routers', array('front'=>$this));
//
//         init default router (articles and 404)
//        $default = new Mage_Core_Controller_Varien_Router_Default();
//        $this->addRouter('default', $default);

        Varien_Profiler::stop('ctrl/init');

        return $this;
    }

    public function dispatch()
    {
        Varien_Profiler::start('ctrl/dispatch');

        $request = $this->getRequest();
        $request->setPathInfo()->setDispatched(false);

        Mage::getModel('core/url_rewrite')->rewrite();
        $this->rewrite();

        Varien_Profiler::stop('app/init');

        $i = 0;
        while (!$request->isDispatched() && $i++<100) {
            foreach ($this->_routers as $router) {
                if ($router->match($this->getRequest())) {
                    break;
                }
            }
        }
        if ($i>100) {
            Mage::throwException('Front controller reached 100 router match iterations');
        }

        Varien_Profiler::stop('ctrl/dispatch');

        Varien_Profiler::start('ctrl/response');
        $this->getResponse()->sendResponse();
        Varien_Profiler::stop('ctrl/response');

        return $this;
    }

    public function getRouterByRoute($routeName)
    {
        // empty route supplied - return base url
        if (empty($routeName)) {
            $router = $this->getRouter('standard');
        } elseif ($this->getRouter('admin')->getFrontNameByRoute($routeName)) {
            // try standard router url assembly
            $router = $this->getRouter('admin');
        } elseif ($this->getRouter('standard')->getFrontNameByRoute($routeName)) {
            // try standard router url assembly
            $router = $this->getRouter('standard');
        } elseif ($router = $this->getRouter($routeName)) {
            // try custom router url assembly
        } else {
            // get default router url
            $router = $this->getRouter('default');
        }

        return $router;
    }

    public function getRouterByFrontName($frontName)
    {
        // empty route supplied - return base url
        if (empty($frontName)) {
            $router = $this->getRouter('standard');
        } elseif ($this->getRouter('admin')->getRouteByFrontName($frontName)) {
            // try standard router url assembly
            $router = $this->getRouter('admin');
        } elseif ($this->getRouter('standard')->getRouteByFrontName($frontName)) {
            // try standard router url assembly
            $router = $this->getRouter('standard');
        } elseif ($router = $this->getRouter($frontName)) {
            // try custom router url assembly
        } else {
            // get default router url
            $router = $this->getRouter('default');
        }

        return $router;
    }

    public function rewrite()
    {
        $request = $this->getRequest();
        $config = Mage::getConfig()->getNode('global/rewrite');
        if (!$config) {
            return;
        }
        foreach ($config->children() as $rewrite) {
            $from = (string)$rewrite->from;
            $to = (string)$rewrite->to;
            if (empty($from) || empty($to)) {
                continue;
            }
            $pathInfo = preg_replace($from, $to, $request->getPathInfo());
            $request->setPathInfo($pathInfo);
        }
    }
}