<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Checkout_AgreementController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/checkoutagreement';

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }

    public function indexAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Terms and Conditions'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/checkout_agreement'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Sales'))->_title($this->__('Terms and Conditions'));

        $id  = $this->getRequest()->getParam('id');
        $agreementModel  = Mage::getModel('checkout/agreement');

        if ($id) {
            $agreementModel->load($id);
            if (!$agreementModel->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('checkout')->__('This condition no longer exists.'),
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($agreementModel->getId() ? $agreementModel->getName() : $this->__('New Condition'));

        $data = Mage::getSingleton('adminhtml/session')->getAgreementData(true);
        if (!empty($data)) {
            $agreementModel->setData($data);
        }

        Mage::register('checkout_agreement', $agreementModel);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('checkout')->__('Edit Condition') : Mage::helper('checkout')->__('New Condition'), $id ? Mage::helper('checkout')->__('Edit Condition') : Mage::helper('checkout')->__('New Condition'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/checkout_agreement_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getSingleton('checkout/agreement');
            $model->setData($postData);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('checkout')->__('The condition has been saved.'));
                $this->_redirect('*/*/');

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('An error occurred while saving this condition.'));
            }

            Mage::getSingleton('adminhtml/session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        $model = Mage::getSingleton('checkout/agreement')
            ->load($id);
        if (!$model->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('This condition no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('checkout')->__('The condition has been deleted'));
            $this->_redirect('*/*/');

            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('An error occurred while deleting this condition.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/checkoutagreement')
            ->_addBreadcrumb(Mage::helper('checkout')->__('Sales'), Mage::helper('checkout')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('checkout')->__('Checkout Conditions'), Mage::helper('checkout')->__('Checkout Terms and Conditions'))
        ;
        return $this;
    }
}
