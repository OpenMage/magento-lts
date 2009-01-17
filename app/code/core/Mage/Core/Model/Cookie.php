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


/**
 * Core cookie model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Cookie
{

    const COOKIE_NAME = 'magento';

    protected $_id = null;

    public function __construct()
    {
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $this->_id = $_COOKIE[self::COOKIE_NAME];
        }
        else {
            $this->_id = $this->randomSequence();
            setcookie(self::COOKIE_NAME, $this->_id, time()+60*60*24*30, $this->getCookiePath(), $this->getCookieDomain());
        }
    }

    public function getCookieDomain()
    {
    	$domain = Mage::getStoreConfig('web/cookie/cookie_domain');
    	if (empty($domain) && isset($_SERVER['HTTP_HOST'])) {
    		$domainArr = explode(':', $_SERVER['HTTP_HOST']);
    		$domain = $domainArr[0];
    	}
    	return $domain;
    }

    public function getCookiePath()
    {
    	$path = Mage::getStoreConfig('web/cookie/cookie_path');
    	if (empty($path)) {
    	    $request = new Mage_Core_Controller_Request_Http();
    	    $path = $request->getBasePath();
    	}
    	return $path;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function randomSequence($length=32)
    {
        $id = '';
        $par = array();
        $char = array_merge(range('a','z'),range(0,9));
        $charLen = count($char)-1;
        for ($i=0;$i<$length;$i++){
            $disc = mt_rand(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id.$char[$disc];
        }
        return $id;
    }

    public function set($cookieName, $value, $period=null)
    {
        if( !isset($period) ) {
            $period = 3600 * 24 * 365;
        }
        $expire = time() + $period;
        $this->delete($cookieName);
        setcookie($cookieName, $value, $expire, $this->getCookiePath(), $this->getCookieDomain());
        return $this;
    }

    public function get($cookieName)
    {
        if( isset($_COOKIE[$cookieName]) ) {
            return $_COOKIE[$cookieName];
        } else {
            return false;
        }
    }

    public function delete($cookieName)
    {
        setcookie($cookieName, '', (time() - 3600) );
        return $this;
    }

}
