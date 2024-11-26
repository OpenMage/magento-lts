<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Billing agreements controller
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method int getAgreementId()
 */
class Mage_Sales_Billing_AgreementController extends Mage_Core_Controller_Front_Action
{
    /**
     * View billing agreements
     */
    public function indexAction()
    {
        $this->_title($this->__('Billing Agreements'));
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    /**
     * Action predispatch
     *
     * Check customer authentication
     *
     * @return $this|void
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!$this->_getSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
        return $this;
    }

    /**
     * View billing agreement
     *
     */
    public function viewAction()
    {
        $agreement = $this->_initAgreement();
        if (!$agreement) {
            $this->_redirect('*/*/index');
            return;
        }

        $customerIdSession = $this->_getSession()->getCustomer()->getId();
        if (!$agreement->canPerformAction($customerIdSession)) {
            $this->_redirect('*/*/index');
            return;
        }

        $this->_title($this->__('Billing Agreements'))
            ->_title($this->__('Billing Agreement # %s', $agreement->getReferenceId()));
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('sales/billing_agreement/');
        }
        $this->renderLayout();
    }

    /**
     * Wizard start action
     *
     * @return $this|void
     */
    public function startWizardAction()
    {
        $agreement = Mage::getModel('sales/billing_agreement');
        $paymentCode = $this->getRequest()->getParam('payment_method');
        if ($paymentCode) {
            try {
                $agreement->setStoreId(Mage::app()->getStore()->getId())
                    ->setMethodCode($paymentCode)
                    ->setReturnUrl(Mage::getUrl('*/*/returnWizard', ['payment_method' => $paymentCode]))
                    ->setCancelUrl(Mage::getUrl('*/*/cancelWizard', ['payment_method' => $paymentCode]));

                $this->_redirectUrl($agreement->initToken());
                return $this;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('Failed to start billing agreement wizard.'));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Wizard return action
     *
     */
    public function returnWizardAction()
    {
        $agreement = Mage::getModel('sales/billing_agreement');
        $paymentCode = $this->getRequest()->getParam('payment_method');
        $token = $this->getRequest()->getParam('token');
        if ($token && $paymentCode) {
            try {
                $agreement->setStoreId(Mage::app()->getStore()->getId())
                    ->setToken($token)
                    ->setMethodCode($paymentCode)
                    ->setCustomer(Mage::getSingleton('customer/session')->getCustomer())
                    ->place();
                $this->_getSession()->addSuccess(
                    $this->__('The billing agreement "%s" has been created.', $agreement->getReferenceId())
                );
                $this->_redirect('*/*/view', ['agreement' => $agreement->getId()]);
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('Failed to finish billing agreement wizard.'));
            }
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Wizard cancel action
     *
     */
    public function cancelWizardAction()
    {
        $this->_redirect('*/*/index');
    }

    /**
     * Cancel action
     * Set billing agreement status to 'Canceled'
     *
     */
    public function cancelAction()
    {
        $agreement = $this->_initAgreement();
        if (!$agreement) {
            $this->_redirect('*/*/view', ['_current' => true]);
            return;
        }

        $customerIdSession = $this->_getSession()->getCustomer()->getId();
        if (!$agreement->canPerformAction($customerIdSession)) {
            $this->_redirect('*/*/view', ['_current' => true]);
            return;
        }

        if ($agreement->canCancel()) {
            try {
                $agreement->cancel();
                $this->_getSession()->addNotice($this->__('The billing agreement "%s" has been canceled.', $agreement->getReferenceId()));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($this->__('Failed to cancel the billing agreement.'));
            }
        }
        $this->_redirect('*/*/view', ['_current' => true]);
    }

    /**
     * Init billing agreement model from request
     *
     * @return Mage_Sales_Model_Billing_Agreement|false
     */
    protected function _initAgreement()
    {
        $agreementId = $this->getRequest()->getParam('agreement');
        if ($agreementId) {
            $billingAgreement = Mage::getModel('sales/billing_agreement')->load($agreementId);
            if (!$billingAgreement->getAgreementId()) {
                $this->_getSession()->addError($this->__('Wrong billing agreement ID specified.'));
                $this->_redirect('*/*/');
                return false;
            }
        }
        Mage::register('current_billing_agreement', $billingAgreement);
        return $billingAgreement;
    }

    /**
     * Retrieve customer session model
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
