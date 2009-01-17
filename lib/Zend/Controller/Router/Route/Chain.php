<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Route.php 1847 2006-11-23 11:36:41Z martel $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
#require_once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * Chain route is used for managing route chaining.
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Controller_Router_Route_Chain extends Zend_Controller_Router_Route_Abstract
{

    protected $_routes = array();
    protected $_separators = array();

    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    { }

    public function chain(Zend_Controller_Router_Route_Interface $route, $separator = '/') {

        $this->_routes[] = $route;
        $this->_separators[] = $separator;

        return $this;

    }

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     *
     * @param Zend_Controller_Request_Http $request Request to get the path info from
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($request, $partial = null)
    {

        $path = $request->getPathInfo();
        
        $values = array();

        foreach ($this->_routes as $key => $route) {
            
            // TODO: Should be an interface method. Hack for 1.0 BC  
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }
            
            $res = $route->match($match);
            if ($res === false) return false;

            $values = $res + $values;

        }

        return $values;
    }

    /**
     * Assembles a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {
        $value = '';

        foreach ($this->_routes as $key => $route) {
            if ($key > 0) {
                $value .= $this->_separators[$key];
            }
            
            $value .= $route->assemble($data, $reset, $encode);
            
            if (method_exists($route, 'getVariables')) {
                $variables = $route->getVariables();
                
                foreach ($variables as $variable) {
                    $data[$variable] = null;
                }
            }
        }

        return $value;
    }

    /**
     * Set the request object for this and the child routes
     * 
     * @param  Zend_Controller_Request_Abstract|null $request
     * @return void
     */
    public function setRequest(Zend_Controller_Request_Abstract $request = null)
    {
        $this->_request = $request;

        foreach ($this->_routes as $route) {
            if (method_exists($route, 'setRequest')) {
                $route->setRequest($request);
            }
        }
    }

}
