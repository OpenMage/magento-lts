<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Custom Zend_Controller_Response_Http class (formally)
 *
 * @package    Mage_Core
 */
class Mage_Core_Controller_Response_Http extends Zend_Controller_Response_Http
{
    /**
     * Transport object for observers to perform
     * @var Varien_Object
     */
    protected static $_transportObject = null;

    /**
     * Fixes CGI only one Status header allowed bug
     * @link  http://bugs.php.net/bug.php?id=36705
     * @inheritDoc
     */
    public function sendHeaders()
    {
        if (!$this->canSendHeaders()) {
            Mage::log('HEADERS ALREADY SENT: ' . mageDebugBacktrace(true, true, true));
            return $this;
        }

        if (str_starts_with(PHP_SAPI, 'cgi')) {
            $statusSent = false;
            foreach ($this->_headersRaw as $i => $header) {
                if (stripos($header, 'status:') === 0) {
                    if ($statusSent) {
                        unset($this->_headersRaw[$i]);
                    } else {
                        $statusSent = true;
                    }
                }
            }
            foreach ($this->_headers as $i => $header) {
                if (strcasecmp($header['name'], 'status') === 0) {
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

    /**
     * @inheritDoc
     */
    public function sendResponse()
    {
        Mage::dispatchEvent('http_response_send_before', ['response' => $this]);
        parent::sendResponse();
    }

    /**
     * Additionally check for session messages in several domains case
     *
     * @inheritDoc
     */
    public function setRedirect($url, $code = 302)
    {
        /**
         * Use single transport object instance
         */
        if (self::$_transportObject === null) {
            self::$_transportObject = new Varien_Object();
        }
        self::$_transportObject->setUrl($url);
        self::$_transportObject->setCode($code);
        Mage::dispatchEvent(
            'controller_response_redirect',
            ['response' => $this, 'transport' => self::$_transportObject],
        );

        return parent::setRedirect(self::$_transportObject->getUrl(), self::$_transportObject->getCode());
    }

    /**
     * Method send already collected headers and exit from script
     * @return void
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function sendHeadersAndExit()
    {
        $this->sendHeaders();
        exit;
    }
}
