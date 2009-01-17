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
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * Retutn Google Base Client Instance
     *
     * @return Zend_Http_Client
     */
    public function getClient()
    {
        $user = Mage::getStoreConfig('google/googlebase/login');
        $pass = Mage::getStoreConfig('google/googlebase/password');

        // Create an authenticated HTTP client
        $errorMsg = Mage::helper('googlebase')->__('Unable to connect to Google Base. Please, check Account settings in configuration.');
        try {
            $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, Zend_Gdata_Gbase::AUTH_SERVICE_NAME);
        } catch (Zend_Gdata_App_AuthException $e) {
            Mage::throwException($errorMsg . Mage::helper('googlebase')->__('Error: %s', $e->getMessage()));
        } catch (Zend_Gdata_App_HttpException $e) {
            Mage::throwException($errorMsg . Mage::helper('googlebase')->__('Error: %s', $e->getMessage()));
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            Mage::throwException($errorMsg . Mage::helper('googlebase')->__('Error: %s', $e->getMessage()));
        }

        return $client;
    }

    /**
     * Retutn Google Base Service Instance
     *
     * @return Zend_Gdata_Gbase
     */
    public function getService()
    {
        if (!$this->_service) {
            $service = $this->_connect();
            $this->_service = $service;
        }
        return $this->_service;
    }

    /**
     * Authorize Google Account
     *
     * @return Zend_Gdata_Gbase
     */
    protected function _connect()
    {
        $client = $this->getClient();
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