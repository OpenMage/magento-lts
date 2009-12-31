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
 * @version    $Id: Route.php 11073 2008-08-26 16:29:59Z dasprid $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Router_Route_Abstract */
#require_once 'Zend/Controller/Router/Route/Abstract.php';

/**
 * Route
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Zend_Controller_Router_Route extends Zend_Controller_Router_Route_Abstract
{

    protected $_urlVariable = ':';
    protected $_urlDelimiter = '/';
    protected $_regexDelimiter = '#';
    protected $_defaultRegex = null;

    /**
     * Holds names of all route's pattern variable names. Array index holds a position in URL.
     * @var array
     */
    protected $_variables = array();

    /**
     * Holds Route patterns for all URL parts. In case of a variable it stores it's regex
     * requirement or null. In case of a static part, it holds only it's direct value.
     * In case of a wildcard, it stores an asterisk (*)
     * @var array
     */
    protected $_parts = array();

    /**
     * Holds user submitted default values for route's variables. Name and value pairs.
     * @var array
     */
    protected $_defaults = array();

    /**
     * Holds user submitted regular expression patterns for route's variables' values.
     * Name and value pairs.
     * @var array
     */
    protected $_requirements = array();

    /**
     * Associative array filled on match() that holds matched path values
     * for given variable names.
     * @var array
     */
    protected $_values = array();

    /**
     * Associative array filled on match() that holds wildcard variable
     * names and values.
     * @var array
     */
    protected $_wildcardData = array();

    /**
     * Helper var that holds a count of route pattern's static parts
     * for validation
     * @var int
     */
    protected $_staticCount = 0;

    public function getVersion() {
        return 1;
    }
    
    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $reqs = ($config->reqs instanceof Zend_Config) ? $config->reqs->toArray() : array();
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        return new self($config->route, $defs, $reqs);
    }

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     */
    public function __construct($route, $defaults = array(), $reqs = array())
    {

        $route = trim($route, $this->_urlDelimiter);
        $this->_defaults = (array) $defaults;
        $this->_requirements = (array) $reqs;

        if ($route != '') {

            foreach (explode($this->_urlDelimiter, $route) as $pos => $part) {

                if (substr($part, 0, 1) == $this->_urlVariable) {
                    $name = substr($part, 1);
                    $this->_parts[$pos] = (isset($reqs[$name]) ? $reqs[$name] : $this->_defaultRegex);
                    $this->_variables[$pos] = $name;
                } else {
                    $this->_parts[$pos] = $part;
                    if ($part != '*') $this->_staticCount++;
                }

            }

        }

    }

    /**
     * Matches a user submitted path with parts defined by a map. Assigns and
     * returns an array of variables on a successful match.
     *
     * @param string $path Path used to match against this routing map
     * @return array|false An array of assigned values or a false on a mismatch
     */
    public function match($path)
    {

        $pathStaticCount = 0;
        $values = array();

        $path = trim($path, $this->_urlDelimiter);
        
        if ($path != '') {

            $path = explode($this->_urlDelimiter, $path);

            foreach ($path as $pos => $pathPart) {

                // Path is longer than a route, it's not a match
                if (!array_key_exists($pos, $this->_parts)) {
                    return false;
                }
                
                // If it's a wildcard, get the rest of URL as wildcard data and stop matching
                if ($this->_parts[$pos] == '*') {
                    $count = count($path);
                    for($i = $pos; $i < $count; $i+=2) {
                        $var = urldecode($path[$i]);
                        if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) {
                            $this->_wildcardData[$var] = (isset($path[$i+1])) ? urldecode($path[$i+1]) : null;
                        }
                    }
                    break;
                }

                $name = isset($this->_variables[$pos]) ? $this->_variables[$pos] : null;
                $pathPart = urldecode($pathPart);

                // If it's a static part, match directly
                if ($name === null && $this->_parts[$pos] != $pathPart) {
                    return false;
                }

                // If it's a variable with requirement, match a regex. If not - everything matches
                if ($this->_parts[$pos] !== null && !preg_match($this->_regexDelimiter . '^' . $this->_parts[$pos] . '$' . $this->_regexDelimiter . 'iu', $pathPart)) {
                    return false;
                }

                // If it's a variable store it's value for later
                if ($name !== null) {
                    $values[$name] = $pathPart;
                } else {
                    $pathStaticCount++;
                }
                
            }

        }

        // Check if all static mappings have been matched
        if ($this->_staticCount != $pathStaticCount) {
            return false;
        }

        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) {
            if (!array_key_exists($var, $return)) {
                return false;
            }
        }

        $this->_values = $values;
        
        return $return;

    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param  array $data An array of variable and value pairs used as parameters
     * @param  boolean $reset Whether or not to set route defaults with those provided in $data
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = false)
    {

        $url = array();
        $flag = false;

        foreach ($this->_parts as $key => $part) {

            $name = isset($this->_variables[$key]) ? $this->_variables[$key] : null;

            $useDefault = false;
            if (isset($name) && array_key_exists($name, $data) && $data[$name] === null) {
                $useDefault = true;
            }

            if (isset($name)) {

                if (isset($data[$name]) && !$useDefault) {
                    $url[$key] = $data[$name];
                    unset($data[$name]);
                } elseif (!$reset && !$useDefault && isset($this->_values[$name])) {
                    $url[$key] = $this->_values[$name];
                } elseif (!$reset && !$useDefault && isset($this->_wildcardData[$name])) {
                    $url[$key] = $this->_wildcardData[$name];
                } elseif (isset($this->_defaults[$name])) {
                    $url[$key] = $this->_defaults[$name];
                } else {
                    #require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception($name . ' is not specified');
                }


            } elseif ($part != '*') {
                $url[$key] = $part;
            } else {
                if (!$reset) $data += $this->_wildcardData;
                foreach ($data as $var => $value) {
                    if ($value !== null) {
                        $url[$key++] = $var;
                        $url[$key++] = $value;
                        $flag = true;
                    }
                }
            }

        }

        $return = '';

        foreach (array_reverse($url, true) as $key => $value) {
            if ($flag || !isset($this->_variables[$key]) || $value !== $this->getDefault($this->_variables[$key])) {
                if ($encode) $value = urlencode($value);
                $return = $this->_urlDelimiter . $value . $return;
                $flag = true;
            }
        }

        return trim($return, $this->_urlDelimiter);

    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
        return null;
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }

}
