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
 * @package     Mage_PaypalUk
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Direct Checkout Controller
 */
class Mage_PaypalUk_DirectController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with paypal direct information
     *
     * @return Mage_PaypalUk_Model_Direct
     */
        public function getDirect()
    {
        return Mage::getSingleton('paypaluk/direct');
    }

    /**
     * Return Validation model
     *
     * @return Mage_PaypalUk_Model_Direct_Validate
     */
    public function getValidation()
    {
        return Mage::getSingleton('paypaluk/direct_validate');
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
        $this->getValidation()
            ->setCcNumber($payment['cc_number'])
            ->setCcExpMonth($payment['cc_exp_month'])
            ->setCcExpYear($payment['cc_exp_year'])
            ->callLookup();

        $result = array();
        if ($this->getValidation()->getEnrolled() == 'Y'
            && !$this->getValidation()->getErrorNo()
            && $this->getValidation()->getAcsUrl()) {
            $result = array('iframeUrl'=>Mage::getUrl('paypaluk/direct/validate', array('_secure' => true)));
        } else {
            $result = array('iframeUrl'=>Mage::getUrl('paypaluk/direct/finalizeValidate', array('_secure' => true)));
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
     * Show finalize page. close iframe and redirect to next step     *
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
        $this->getValidation()
            ->setPaResPayload($PAResPayload)
            ->setMd($MD)
            ->callAuthentication();

        if ($this->getValidation()->getErrorNo()==0
            && $this->getValidation()->getSignature() == 'Y'
            && $this->getValidation()->getPaResStatus() != 'N') {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->loadLayout();
            $this->getLayout()->getBlock('root')->setError($this->getValidation()->getErrorDesc());
            $this->renderLayout();
        }
    }
}
