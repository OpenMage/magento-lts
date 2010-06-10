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
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Item Types Model
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Service extends Varien_Object
{
    /**
     * Client instance identifier in registry
     *
     * @var string
     */
    protected $_clientRegistryId = 'GBASE_HTTP_CLIENT';

    /**
     * Retutn Google Base Client Instance
     *
     * @return Zend_Http_Client
     */
    public function getClient($storeId = null, $loginToken = null, $loginCaptcha = null)
    {
        $user = $this->getConfig()->getAccountLogin($storeId);
        $pass = $this->getConfig()->getAccountPassword($storeId);
        $type = $this->getConfig()->getAccountType($storeId);

        // Create an authenticated HTTP client
        $errorMsg = Mage::helper('googlebase')->__('Unable to connect to Google Base. Please, check Account settings in configuration.');
        try {
            if (! Mage::registry($this->_clientRegistryId)) {
                $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, Zend_Gdata_Gbase::AUTH_SERVICE_NAME, null, '',
                    $loginToken, $loginCaptcha,
                    Zend_Gdata_ClientLogin::CLIENTLOGIN_URI,
                    $type
                );
                Mage::register($this->_clientRegistryId, $client);
            }
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            throw $e;
        } catch (Zend_Gdata_App_HttpException $e) {
            Mage::throwException($errorMsg . Mage::helper('googlebase')->__('Error: %s', $e->getMessage()));
        } catch (Zend_Gdata_App_AuthException $e) {
            Mage::throwException($errorMsg . Mage::helper('googlebase')->__('Error: %s', $e->getMessage()));
        }

        return Mage::registry($this->_clientRegistryId);
    }

    /**
     * Retutn Google Base Service Instance
     *
     * @return Zend_Gdata_Gbase
     */
    public function getService($storeId = null)
    {
        if (!$this->_service) {
            $service = $this->_connect($storeId);
            $this->_service = $service;
        }
        return $this->_service;
    }

    /**
     * Retutn Google Base Anonymous Client Instance
     *
     * @return Zend_Gdata_Gbase
     */
    public function getGuestService()
    {
        return new Zend_Gdata_Gbase(new Zend_Http_Client());
    }

    /**
     * Google Base Config
     *
     * @return Mage_GoogleBase_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('googlebase/config');
    }

    /**
     * Authorize Google Account
     *
     * @return Zend_Gdata_Gbase
     */
    protected function _connect($storeId = null)
    {
        $client = $this->getClient($storeId);
        $service = new Zend_Gdata_Gbase($client);
        return $service;
    }

    /**
     * Get Dry Run mode
     *
     * @return boolean
     */
    public function getDryRun()
    {
        return $this->getDataSetDefault('dry_run', false);
    }
}
