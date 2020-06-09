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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment bridge controller
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_PbridgeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Load only action layout handles
     *
     * @return $this
     */
    protected function _initActionLayout()
    {
        if (!$this->_checkPbridge()) {
            return;
        }
        $this->_checkPbridge();
        $this->addActionLayoutHandles();
        $this->loadLayoutUpdates();
        $this->generateLayoutXml();
        $this->generateLayoutBlocks();
        $this->_isLayoutLoaded = true;
        return $this;
    }

    /**
     * Check is available Payment Bridge module
     *
     * @return bool
     */
    protected function _checkPbridge()
    {
        if (!is_object(Mage::getConfig()->getNode('modules/Enterprise_Pbridge'))) {
            $this->getResponse()->setBody($this->__('Payment Bridge module unavailable.'));
            return false;
        }
        return true;
    }

    /**
     * Index Action.
     * Forward to result action
     *
     * @return null
     */
    public function indexAction()
    {
        $this->_forward('result');
    }

    /**
     * Result Action
     *
     * @return null
     */
    public function resultAction()
    {
        $this->_initActionLayout();
        $this->renderLayout();
    }

    /**
     * Output action with params that was given by payment bridge
     *
     * @return null
     */
    public function outputAction()
    {
        if (!$this->_checkPbridge()) {
            return;
        }
        $this->loadLayout(false);

        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        $method = $helper->escapeHtml($this->getRequest()->getParam('method', false));
        $originalPaymentMethod = $helper->escapeHtml($this->getRequest()->getParam('original_payment_method', false));
        $token = $helper->escapeHtml($this->getRequest()->getParam('token', false));
        $ccLast4 = $helper->escapeHtml($this->getRequest()->getParam('cc_last4', false));
        $ccType  = $helper->escapeHtml($this->getRequest()->getParam('cc_type', false));

        if ($originalPaymentMethod && $token && $ccLast4 && $ccType) {
            $message = Mage::helper('enterprise_pbridge')->__('Payment Bridge Selected');
            $methodName = 'payment[pbridge_data][original_payment_method]';
            $inputType = '<input type="hidden"';
            $body = <<<EOT
    <div id="payment_form_{$method}">
        {$message}
        {$inputType} id="{$method}_original_payment_method" name="{$methodName}" value="{$originalPaymentMethod}">
        {$inputType} id="{$method}_token" name="payment[pbridge_data][token]" value="{$token}">
        {$inputType} id="{$method}_cc_last4" name="payment[pbridge_data][cc_last4]" value="{$ccLast4}">
        {$inputType} id="{$method}_cc_type" name="payment[pbridge_data][cc_type]" value="{$ccType}">
    </div>
EOT;
        } else {
            $message = $this->__('Error while reading data from Payment Bridge. Please, try again.');
            $body = <<<EOT
    <div id="payment_form_error">
        {$message}
    </div>
EOT;
        }
        $replacePattern = '{{content}}';
        $content = html_entity_decode(Mage::helper('xmlconnect')->htmlize($replacePattern));
        $this->getResponse()->setBody(str_replace($replacePattern, $body, $content));
    }
}
