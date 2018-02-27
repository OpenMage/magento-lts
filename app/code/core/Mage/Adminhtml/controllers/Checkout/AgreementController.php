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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Checkout_AgreementController extends Mage_Adminhtml_Controller_Action
{
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
                    Mage::helper('checkout')->__('This condition no longer exists.')
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
            ->_addBreadcrumb($id ? Mage::helper('checkout')->__('Edit Condition') :  Mage::helper('checkout')->__('New Condition'), $id ?  Mage::helper('checkout')->__('Edit Condition') :  Mage::helper('checkout')->__('New Condition'))
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
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('checkout')->__('An error occurred while saving this condition.'));
            }

            Mage::getSingleton('adminhtml/session')->setAgreementData($postData);
            $this->_redirectReferer();
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
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
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
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

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/checkoutagreement');
    }
}
