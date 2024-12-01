<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Centinel
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Centinel Index Controller
 *
 * @category   Mage
 * @package    Mage_Centinel
 */
class Mage_Centinel_Adminhtml_Centinel_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/order/actions/review_payment';

    /**
     * Process validate payment data action
     *
     */
    public function validatePaymentDataAction()
    {
        $result = [];
        try {
            $paymentData = $this->getRequest()->getParam('payment');
            $validator = $this->_getValidator();
            if (!$validator) {
                throw new Exception('This payment method does not have centinel validation.');
            }
            $validator->reset();
            $this->_getPayment()->importData($paymentData);
            $result['authenticationUrl'] = $validator->getAuthenticationStartUrl();
        } catch (Mage_Core_Exception $e) {
            $result['message'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['message'] = Mage::helper('centinel')->__('Validation failed.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Process authentication start action
     *
     */
    public function authenticationStartAction()
    {
        if ($validator = $this->_getValidator()) {
            Mage::register('current_centinel_validator', $validator);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Process authentication complete action
     *
     */
    public function authenticationCompleteAction()
    {
        try {
            if ($validator = $this->_getValidator()) {
                $request = $this->getRequest();

                $data = new Varien_Object();
                $data->setTransactionId($request->getParam('MD'));
                $data->setPaResPayload($request->getParam('PaRes'));

                $validator->authenticate($data);
                Mage::register('current_centinel_validator', $validator);
            }
        } catch (Exception $e) {
            Mage::register('current_centinel_validator', false);
        }
        $this->loadLayout()->renderLayout();
    }

    /**
     * Return payment model
     *
     * @return Mage_Sales_Model_Quote_Payment
     */
    private function _getPayment()
    {
        $model = Mage::getSingleton('adminhtml/sales_order_create');
        return $model->getQuote()->getPayment();
    }

    /**
     * Return Centinel validation model
     *
     * @return Mage_Centinel_Model_Service|false
     */
    private function _getValidator()
    {
        if ($this->_getPayment()->getMethodInstance()->getIsCentinelValidationEnabled()) {
            return $this->_getPayment()->getMethodInstance()->getCentinelValidator();
        }
        return false;
    }
}
