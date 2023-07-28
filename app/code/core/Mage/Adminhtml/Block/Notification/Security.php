<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Security extends Mage_Adminhtml_Block_Template
{
    // Cache kay for saving verification result
    public const VERIFICATION_RESULT_CACHE_KEY = 'configuration_files_access_level_verification';

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
