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
 * @package     Mage_Captcha
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer
{
    /**
     * Check Captcha On Forgot Password Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkForgotpassword($observer)
    {
        $formId = 'user_forgotpassword';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLogin($observer)
    {
        $formId = 'user_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $loginParams = $controller->getRequest()->getPost('login');
        $login = isset($loginParams['username']) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            $word = $this->_getCaptchaString($controller->getRequest(), $formId);
            if (!$captchaModel->isCorrect($word)) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setUsername($login);
                $beforeUrl = Mage::getSingleton('customer/session')->getBeforeAuthUrl();
                $url =  $beforeUrl ? $beforeUrl : Mage::helper('customer')->getLoginUrl();
                $controller->getResponse()->setRedirect($url);
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Check Captcha On Register User Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserCreate($observer)
    {
        $formId = 'user_create';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                Mage::getSingleton('customer/session')->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*/create'));
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout as Guest Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkGuestCheckout($observer)
    {
        $formId = 'guest_checkout';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On Checkout Register Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkRegisterCheckout($observer)
    {
        $formId = 'register_during_checkout';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $checkoutMethod = Mage::getSingleton('checkout/type_onepage')->getQuote()->getCheckoutMethod();
        if ($checkoutMethod == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            if ($captchaModel->isRequired()) {
                $controller = $observer->getControllerAction();
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $result = array('error' => 1, 'message' => Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                    $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                }
            }
        }
        return $this;
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserLoginBackend($observer)
    {
        $formId = 'backend_login';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $loginParams = Mage::app()->getRequest()->getPost('login', array());
        $login = array_key_exists('username', $loginParams) ? $loginParams['username'] : null;
        if ($captchaModel->isRequired($login)) {
            if (!$captchaModel->isCorrect($this->_getCaptchaString(Mage::app()->getRequest(), $formId))) {
                $captchaModel->logAttempt($login);
                Mage::throwException(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
            }
        }
        $captchaModel->logAttempt($login);
        return $this;
    }

    /**
     * Returns backend session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getBackendSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Check Captcha On User Login Backend Page
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkUserForgotPasswordBackend($observer)
    {
        $formId = 'backend_forgotpassword';
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        $controller = $observer->getControllerAction();
        $email = (string) $observer->getControllerAction()->getRequest()->getParam('email');
        $params = $observer->getControllerAction()->getRequest()->getParams();

        if (!empty($email) && !empty($params)){
            if ($captchaModel->isRequired()){
                if (!$captchaModel->isCorrect($this->_getCaptchaString($controller->getRequest(), $formId))) {
                    $this->_getBackendSession()->setEmail((string) $controller->getRequest()->getPost('email'));
                    $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $this->_getBackendSession()->addError(Mage::helper('captcha')->__('Incorrect CAPTCHA.'));
                    $controller->getResponse()->setRedirect(Mage::getUrl('*/*/forgotpassword'));
                }
            }
        }
        return $this;
    }

    /**
     * Reset Attempts For Frontend
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function resetAttemptForFrontend($observer)
    {
        return $this->_resetAttempt($observer->getModel()->getEmail());
    }

    /**
     * Reset Attempts For Backend
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function resetAttemptForBackend($observer)
    {
        return $this->_resetAttempt($observer->getUser()->getUsername());
    }

    /**
     * Delete Unnecessary logged attempts
     *
     * @return Mage_Captcha_Model_Observer
     */
    public function deleteOldAttempts()
    {
        Mage::getResourceModel('captcha/log')->deleteOldAttempts();
        return $this;
    }

    /**
     * Delete Expired Captcha Images
     *
     * @return Mage_Captcha_Model_Observer
     */
    public function deleteExpiredImages()
    {
        foreach (Mage::app()->getWebsites(true) as $website){
            $expire = time() - Mage::helper('captcha')->getConfigNode('timeout', $website->getDefaultStore())*60;
            $imageDirectory = Mage::helper('captcha')->getImgDir($website);
            foreach (new DirectoryIterator($imageDirectory) as $file) {
                if ($file->isFile() && pathinfo($file->getFilename(), PATHINFO_EXTENSION) == 'png') {
                    if ($file->getMTime() < $expire) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Reset Attempts
     *
     * @param string $login
     * @return Mage_Captcha_Model_Observer
     */
    protected function _resetAttempt($login)
    {
        Mage::getResourceModel('captcha/log')->deleteUserAttempts($login);
        return $this;
    }

    /**
     * Get Captcha String
     *
     * @param Varien_Object $request
     * @param string $formId
     * @return string
     */
    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }
}
