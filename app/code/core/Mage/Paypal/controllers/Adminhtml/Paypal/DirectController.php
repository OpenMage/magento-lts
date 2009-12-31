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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Direct Admin Checkout Controller
 *
 */
class Mage_Paypal_Adminhtml_Paypal_DirectController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return Validation model
     * //@protected
     * @return Mage_Paypal_Model_Direct_Validate
     */
    protected function _getValidation()
    {
        return Mage::getSingleton('paypal/direct_validate');
    }

    /**
     * Process lookup action.
     * Get post data, paste to cmpi_lookup method, analyze result,
     * decide  witch url need to return. If lookup process passed - return validate form url,
     * if api not respond - return success url
     *
     */
    public function lookupAction()
    {
        $payment = $this->getRequest()->getParam('payment');

        $this->_getValidation()
            ->setCcNumber($payment['cc_number'])
            ->setCcExpMonth($payment['cc_exp_month'])
            ->setCcExpYear($payment['cc_exp_year'])
            ->callLookup();

        $result = array();
        if ($this->_getValidation()->getEnrolled() == 'Y'
            && !$this->_getValidation()->getErrorNo()
            && $this->_getValidation()->getAcsUrl()) {
            $result = array('iframeUrl' => $this->getUrl('*/paypal_direct/validate', array('_current' => true, '_secure' => true)));
        } else {
            $result = array('iframeUrl' => $this->getUrl('*/paypal_direct/finalizeValidate', array('_current' => true, '_secure' => true)));
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    /**
     * Load auto submit form, for sending customer to card holder validation page
     *
     */
    public function validateAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Show finalize page. close iframe and redirect to next step
     *
     */
    public function finalizeValidateAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Loaded as callback url from cardholder validation page
     * analize incoming data, call cmpi_authorization method, analize result.
     * show finalize page with success result or error message
     *
     */
    public function termValidateAction()
    {
        $PAResPayload = $this->getRequest()->getParam('PaRes');
        $MD = $this->getRequest()->getParam('MD');
        $this->_getValidation()
            ->setPaResPayload($PAResPayload)
            ->setMd($MD)
            ->callAuthentication();

        if ($this->_getValidation()->getErrorNo()==0
            && $this->_getValidation()->getSignature() == 'Y'
            && $this->_getValidation()->getPaResStatus() != 'N') {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->loadLayout();
            $this->getLayout()->getBlock('root')->setError($this->_getValidation()->getErrorDesc());
            $this->renderLayout();
        }

    }
}
