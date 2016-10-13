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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Adminhtml_Block_Notification_Curl
 */
class Mage_Adminhtml_Block_Notification_Curl extends Mage_Adminhtml_Block_Template
{
    /**
     * Required version of cURL.
     */
    const REQUIRED_CURL_VERSION = '7.34.0';

    /**
     * Information about cURL version.
     *
     * @var array
     */
    protected $_curlVersion;

    public function __construct()
    {
        $this->_curlVersion = curl_version();
    }

    /**
     * Check cURL version and return true if system must show notification message.
     *
     * @return bool
     */
    protected function _canShow()
    {
        $result = false;
        if (
            $this->getRequest()->getParam('section') == 'payment'
            && !version_compare($this->_curlVersion['version'], $this::REQUIRED_CURL_VERSION, '>=')
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Returns a message that should be displayed.
     *
     * @return string
     */
    public function getMessage()
    {
        $message = $this->helper('adminhtml')->__(
            "Your current version of cURL php5 module is %s, which can prevent services that require TLS v1.2 from working correctly. It is recommended to update your cURL php5 module to version %s or higher.",
            $this->_curlVersion['version'],
            $this::REQUIRED_CURL_VERSION
        );

        return $message;
    }

    /**
     * Prepare html output.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_canShow()) {
            return '';
        }

        return parent::_toHtml();
    }
}
