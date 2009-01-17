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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function authFrontend()
    {
        $session = Mage::getSingleton('rss/session');
        if ($session->isCustomerLoggedIn()) {
            return;
        }
        list($username, $password) = $this->authValidate();
        $customer = Mage::getModel('customer/customer')->authenticate($username, $password);
        if ($customer && $customer->getId()) {
            Mage::getSingleton('rss/session')->settCustomer($customer);
        } else {
            $this->authFailed();
        }
    }

    public function authAdmin($path)
    {
        $session = Mage::getSingleton('rss/session');
        if ($session->isAdminLoggedIn()) {
            return;
        }
        list($username, $password) = $this->authValidate();
        $adminSession = Mage::getModel('admin/session');
        $user = $adminSession->login($username, $password);
        //$user = Mage::getModel('admin/user')->login($username, $password);
        if($user && $user->getId() && $user->getIsActive() == '1' && $adminSession->isAllowed($path)){
            $session->setAdmin($user);
        } else {
            $this->authFailed();
        }
    }

    public function authValidate($headers=null)
    {
        if(!is_null($headers)) {
            $_SERVER = $headers;
        }

        $user = '';
        $pass = '';
        // moshe's fix for CGI
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            foreach ($_SERVER as $k=>$v) {
                if (substr($k, -18)==='HTTP_AUTHORIZATION' && !empty($v)) {
                    $_SERVER['HTTP_AUTHORIZATION'] = $v;
                    break;
                }
            }
        }

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
            $pass = $_SERVER['PHP_AUTH_PW'];
        }
        //  IIS Note::  For HTTP Authentication to work with IIS,
        // the PHP directive cgi.rfc2616_headers must be set to 0 (the default value).
        elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }
        elseif (!empty($_SERVER['Authorization'])) {
            $auth = $_SERVER['Authorization'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }

        if(!$user || !$pass) {
            $this->authFailed();
        }

        return array($user, $pass);
    }

    public function authFailed()
    {
        Mage::app()->getResponse()
            ->setHeader('HTTP/1.1','401 Unauthorized')
            ->setHeader('WWW-Authenticate','Basic realm="RSS Feeds"')
            ->setBody('<h1>401 Unauthorized</h1>')
            ->sendResponse();
        exit;
    }
}