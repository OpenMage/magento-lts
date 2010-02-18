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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax rule controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Tax_RuleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Manage Tax Rules'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule'))
            ->renderLayout();
        return $this;
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Manage Tax Rules'));

        $taxRuleId  = $this->getRequest()->getParam('rule');
        $ruleModel  = Mage::getModel('tax/calculation_rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                Mage::getSingleton('adminhtml/session')->unsRuleData();
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('This rule no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf("%s", $ruleModel->getCode()) : $this->__('New Rule'));

        Mage::register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb($taxRuleId ? Mage::helper('tax')->__('Edit Rule') :  Mage::helper('tax')->__('New Rule'), $taxRuleId ?  Mage::helper('tax')->__('Edit Rule') :  Mage::helper('tax')->__('New Rule'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_edit')->setData('action', $this->getUrl('*/tax_rule/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $ruleModel = Mage::getSingleton('tax/calculation_rule');
            $ruleModel->setData($postData);

            try {
                $ruleModel->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Tax rule was successfully saved'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('rule' => $ruleModel->getId()));
                    return;
                }

                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error while saving this tax rule. Please try again later.'));
            }

            Mage::getSingleton('adminhtml/session')->setRuleData($postData);
            $this->_redirectReferer();
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
    }

    public function deleteAction()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule');
        $ruleModel = Mage::getSingleton('tax/calculation_rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tax')->__('Tax rule was successfully deleted'));
            $this->_redirect('*/*/');

            return;
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('Error while deleting this tax rule. Please try again later.'));
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
            ->_setActiveMenu('sales/tax/rule')
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax'), Mage::helper('tax')->__('Tax'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax Rules'), Mage::helper('tax')->__('Tax Rules'))
        ;
        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/tax/rules');
    }
}
