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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variables admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_System_VariableController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/variable';

    /**
     * Initialize Layout and set breadcrumbs
     *
     * @return $this
     */
    protected function _initLayout()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/variable')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Custom Variables'), Mage::helper('adminhtml')->__('Custom Variables'));
        return $this;
    }

    /**
     * Initialize Variable object
     *
     * @return Mage_Core_Model_Variable
     * @throws Mage_Core_Exception
     */
    protected function _initVariable()
    {
        $this->_title($this->__('System'))->_title($this->__('Custom Variables'));

        $variableId = $this->getRequest()->getParam('variable_id', null);
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        /** @var Mage_Core_Model_Variable $variable */
        $variable = Mage::getModel('core/variable');
        if ($variableId) {
            $variable->setStoreId($storeId)
                ->load($variableId);
        }
        Mage::register('current_variable', $variable);
        return $variable;
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Custom Variables'));

        $this->_initLayout()
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_variable'))
            ->renderLayout();
    }

    /**
     * New Action (forward to edit action)
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit Action
     * @throws Mage_Core_Exception
     */
    public function editAction()
    {
        $variable = $this->_initVariable();

        $this->_title($variable->getId() ? $variable->getCode() : $this->__('New Variable'));

        $this->_initLayout()
            ->_addContent($this->getLayout()->createBlock('adminhtml/system_variable_edit'))
            ->_addJs($this->getLayout()->createBlock('core/template', '', [
                'template' => 'system/variable/js.phtml',
            ]))
            ->renderLayout();
    }

    /**
     * Validate Action
     * @throws Mage_Core_Exception
     */
    public function validateAction()
    {
        $response = new Varien_Object(['error' => false]);
        $variable = $this->_initVariable();
        $variable->addData($this->getRequest()->getPost('variable'));
        $result = $variable->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save Action
     * @throws Mage_Core_Exception|Throwable
     */
    public function saveAction()
    {
        $variable = $this->_initVariable();
        $data = $this->getRequest()->getPost('variable');
        $back = $this->getRequest()->getParam('back', false);
        if ($data) {
            $data['variable_id'] = $variable->getId();
            $variable->setData($data);
            try {
                $variable->save();
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('The custom variable has been saved.'),
                );
                if ($back) {
                    $this->_redirect('*/*/edit', ['_current' => true, 'variable_id' => $variable->getId()]);
                } else {
                    $this->_redirect('*/*/', []);
                }
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['_current' => true,]);
                return;
            }
        }
        $this->_redirect('*/*/', []);
    }

    /**
     * Delete Action
     * @throws Mage_Core_Exception|Throwable
     */
    public function deleteAction()
    {
        $variable = $this->_initVariable();
        if ($variable->getId()) {
            try {
                $variable->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('adminhtml')->__('The custom variable has been deleted.'),
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['_current' => true,]);
                return;
            }
        }
        $this->_redirect('*/*/', []);
    }

    /**
     * WYSIWYG Plugin Action
     *
     */
    public function wysiwygPluginAction()
    {
        $customVariables = Mage::getModel('core/variable')->getVariablesOptionArray(true);
        $storeContactVariabls = Mage::getModel('core/source_email_variables')->toOptionArray(true);
        $variables = [$storeContactVariabls, $customVariables];
        $this->getResponse()->setBody(Zend_Json::encode($variables));
    }
}
