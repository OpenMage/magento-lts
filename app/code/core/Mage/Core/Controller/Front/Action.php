<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Carbon\Carbon;

/**
 * Base front controller
 *
 * @package    Mage_Core
 */
class Mage_Core_Controller_Front_Action extends Mage_Core_Controller_Varien_Action
{
    /**
     * Session namespace to refer in other places
     */
    public const SESSION_NAMESPACE = 'om_frontend';

    /**
     * Add secret key to url config path
     */
    public const XML_CSRF_USE_FLAG_CONFIG_PATH   = 'system/csrf/use_form_key';

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * Predispatch: should set layout area
     *
     * @return $this
     */
    public function preDispatch()
    {
        $this->getLayout()->setArea($this->_currentArea);

        parent::preDispatch();
        return $this;
    }

    /**
     * Postdispatch: should set last visited url
     *
     * @return $this
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION)) {
            Mage::getSingleton('core/session')->setLastUrl(Mage::getUrl('*/*/*', ['_current' => true]));
        }

        return $this;
    }

    /**
     * Translate a phrase
     *
     * @return string
     * @SuppressWarnings("PHPMD.CamelCaseMethodName")
     * @SuppressWarnings("PHPMD.ShortMethodName")
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getRealModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }

    /**
     * Declare headers and content file in response for file download
     *
     * @param  string       $fileName
     * @param  array|string $content       set to null to avoid starting output, $contentLength should be set explicitly in
     *                                     that case
     * @param  string       $contentType
     * @param  int          $contentLength explicit content length, if strlen($content) isn't applicable
     * @return $this
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    protected function _prepareDownloadResponse(
        $fileName,
        $content,
        $contentType = 'application/octet-stream',
        $contentLength = null
    ) {
        $session = Mage::getSingleton('admin/session');
        if ($session->isFirstPageAfterLogin()) {
            $this->_redirect($session->getUser()->getStartupPageUrl());
            return $this;
        }

        $isFile = false;
        $file   = null;
        if (is_array($content)) {
            if (!isset($content['type']) || !isset($content['value'])) {
                return $this;
            }

            if ($content['type'] == 'filename') {
                $isFile         = true;
                $file           = $content['value'];
                $contentLength  = filesize($file);
            }
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', is_null($contentLength) ? strlen($content) : $contentLength)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Last-Modified', Carbon::now()->format('r'));

        if (!is_null($content)) {
            if ($isFile) {
                $this->getResponse()->clearBody();
                $this->getResponse()->sendHeaders();

                $ioAdapter = new Varien_Io_File();
                if (!$ioAdapter->fileExists($file)) {
                    Mage::throwException(Mage::helper('core')->__('File not found'));
                }

                $ioAdapter->open(['path' => $ioAdapter->dirname($file)]);
                $ioAdapter->streamOpen($file, 'r');
                while ($buffer = $ioAdapter->streamRead()) {
                    print $buffer;
                }

                $ioAdapter->streamClose();
                if (!empty($content['rm'])) {
                    $ioAdapter->rm($file);
                }

                exit(0);
            }

            $this->getResponse()->setBody($content);
        }

        return $this;
    }

    /**
     * Validate Form Key
     *
     * @return bool
     */
    protected function _validateFormKey()
    {
        $validated = true;
        if ($this->_isFormKeyEnabled()) {
            return parent::_validateFormKey();
        }

        return $validated;
    }

    /**
     * Check if form key validation is enabled.
     *
     * @return bool
     */
    protected function _isFormKeyEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CSRF_USE_FLAG_CONFIG_PATH);
    }

    /**
     * Check if form_key validation enabled on checkout process
     *
     * @return bool
     */
    protected function isFormkeyValidationOnCheckoutEnabled()
    {
        return Mage::getStoreConfigFlag('admin/security/validate_formkey_checkout');
    }
}
