<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Security extends Mage_Adminhtml_Block_Template
{
    // Cache kay for saving verification result
    const VERIFICATION_RESULT_CACHE_KEY = 'configuration_files_access_level_verification';

    /**
     * File path for verification
     * @var string
     */
    protected $_filePath = 'app/etc/local.xml';

    /**
     * Time out for HTTP verification request
     * @var int
     */
    protected $_verificationTimeOut  = 2;

    /**
     * Check verification result and return true if system must to show notification message
     *
     * @return bool
     */
    protected function _canShowNotification()
    {
        if (Mage::app()->loadCache(self::VERIFICATION_RESULT_CACHE_KEY)) {
            return false;
        }
        if ($this->_isFileAccessible()) {
            return true;
        }
        $adminSessionLifetime = (int)Mage::getStoreConfig('admin/security/session_cookie_lifetime');
        Mage::app()->saveCache(true, self::VERIFICATION_RESULT_CACHE_KEY, [], $adminSessionLifetime);
        return false;
    }

    /**
     * If file is accessible return true or false
     *
     * @return bool
     */
    protected function _isFileAccessible()
    {
        $defaultUnsecureBaseURL = (string) Mage::getConfig()->getNode('default/' . Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL);

        $http = new Varien_Http_Adapter_Curl();
        $http->setConfig(['timeout' => $this->_verificationTimeOut]);
        $http->write(Zend_Http_Client::POST, $defaultUnsecureBaseURL . $this->_filePath);
        $responseBody = $http->read();
        $responseCode = Zend_Http_Response::extractCode($responseBody);
        $http->close();

        return $responseCode == 200;
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_canShowNotification()) {
            return '';
        }
        return parent::_toHtml();
    }
}
