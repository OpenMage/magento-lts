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
 * @category   Varien
 * @package    Varien_Object
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maged_View
{
    protected $_data = array();

    public function __construct()
    {

    }

    public function controller()
    {
        return Maged_Controller::singleton();
    }

    public function url($action='', $params=array())
    {
        return $this->controller()->url($action, $params);
    }

    public function baseUrl()
    {
        return str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    }

    public function mageUrl()
    {
        return str_replace('\\', '/', dirname($this->baseUrl()));
    }

    public function template($name)
    {
        ob_start();
        include $this->controller()->filepath('template/'.$name);
        return ob_get_clean();
    }

    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function __($string)
    {
        return $string;
    }

    public function getNavLinkParams($action)
    {
        $params = 'href="'.$this->url($action).'"';
        if ($this->controller()->getAction()==$action) {
            $params .= ' class="active"';
        }
        return $params;
    }
}
