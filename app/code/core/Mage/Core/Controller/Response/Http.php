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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Custom Zend_Controller_Response_Http class (formally)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Response_Http extends Zend_Controller_Response_Http
{
    /**
     * Transport object for observers to perform
     *
     * @var Varien_Object
     */
    protected static $_transportObject = null;

    /**
     * Fixes CGI only one Status header allowed bug
     *
     * @link  http://bugs.php.net/bug.php?id=36705
     *
     * {@inheritDoc}
     */
    public function sendHeaders()
    {
        if (!$this->canSendHeaders()) {
            Mage::log('HEADERS ALREADY SENT: '.mageDebugBacktrace(true, true, true));
            return $this;
        }

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            $statusSent = false;
            foreach ($this->_headersRaw as $i => $header) {
                if (stripos($header, 'status:')===0) {
                    if ($statusSent) {
                        unset($this->_headersRaw[$i]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
            foreach ($this->_headers as $i => $header) {
                if (strcasecmp($header['name'], 'status')===0) {
                    if ($statusSent) {
                        unset($this->_headers[$i]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
        }

        return parent::sendHeaders();
    }

    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', array('response'=>$this));
        return parent::sendResponse();
    }

    /**
     * Additionally check for session messages in several domains case
     *
     * {@inheritDoc}
     */
    public function setRedirect($url, $code = 302)
    {
        /**
         * Use single transport object instance
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Varien_Object;
        }
        self::$_transportObject->setData('url', $url);
        self::$_transportObject->setData('code', $code);
        Mage::dispatchEvent(
            'controller_response_redirect',
            array('response' => $this, 'transport' => self::$_transportObject)
        );

        return parent::setRedirect(self::$_transportObject->getData('url'), self::$_transportObject->getData('code'));
    }

    /**
     * Method send already collected headers and exit from script
     */
    public function sendHeadersAndExit()
    {
        $this->sendHeaders();
        exit;
    }
}
