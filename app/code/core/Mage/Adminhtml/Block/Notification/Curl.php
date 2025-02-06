<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Class Mage_Adminhtml_Block_Notification_Curl
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Curl extends Mage_Adminhtml_Block_Template
{
    /**
     * Required version of cURL.
     */
    public const REQUIRED_CURL_VERSION = '7.34.0';

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
        if ($this->getRequest()->getParam('section') == 'payment'
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
        return $this->helper('adminhtml')->__(
            'Your current version of cURL php5 module is %s, which can prevent services that require TLS v1.2 from working correctly. It is recommended to update your cURL php5 module to version %s or higher.',
            $this->_curlVersion['version'],
            $this::REQUIRED_CURL_VERSION,
        );
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
