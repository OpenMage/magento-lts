<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 attribute controller
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Adminhtml_Api2_AttributeController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'system/api';

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions(['save']);
        return parent::preDispatch();
    }

    /**
     * Show user types grid
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Web Services'))
             ->_title($this->__('REST Attributes'));

        $this->loadLayout()->_setActiveMenu('system/api/rest_attributes');

        $this->_addBreadcrumb($this->__('Web services'), $this->__('Web services'))
            ->_addBreadcrumb($this->__('REST Attributes'), $this->__('REST Attributes'))
            ->_addBreadcrumb($this->__('Attributes'), $this->__('Attributes'));

        $this->renderLayout();
    }

    /**
     * Edit role
     */
    public function editAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/api/rest_attributes');

        $type = $this->getRequest()->getParam('type');

        $userTypes = Mage_Api2_Model_Auth_User::getUserTypes();
        if (!isset($userTypes[$type])) {
            $this->_getSession()->addError($this->__('User type "%s" not found.', $type));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($this->__('System'))
            ->_title($this->__('Web Services'))
            ->_title($this->__('REST ACL Attributes'));

        $title = $this->__('Edit %s ACL attribute rules', $userTypes[$type]);
        $this->_title($title);
        $this->_addBreadcrumb($title, $title);

        $this->renderLayout();
    }

    /**
     * Save role
     */
    public function saveAction()
    {
        $request = $this->getRequest();

        $type = $request->getParam('type');

        if (!$type) {
            $this->_getSession()->addError(
                $this->__('User type "%s" no longer exists', $type)
            );
            $this->_redirect('*/*/');
            return;
        }

        $session = $this->_getSession();

        try {
            /** @var Mage_Api2_Model_Acl_Global_Rule_Tree $ruleTree */
            $ruleTree = Mage::getSingleton(
                'api2/acl_global_rule_tree',
                ['type' => Mage_Api2_Model_Acl_Global_Rule_Tree::TYPE_ATTRIBUTE]
            );

            /** @var Mage_Api2_Model_Acl_Filter_Attribute $attribute */
            $attribute = Mage::getModel('api2/acl_filter_attribute');

            $collection = $attribute->getCollection();
            $collection->addFilterByUserType($type);

            /** @var Mage_Api2_Model_Acl_Filter_Attribute $model */
            foreach ($collection as $model) {
                $model->delete();
            }

            foreach ($ruleTree->getPostResources() as $resourceId => $operations) {
                if (Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL === $resourceId) {
                    $attribute->setUserType($type)
                        ->setResourceId($resourceId)
                        ->save();
                } else {
                    foreach ($operations as $operation => $attributes) {
                        $attribute->setId(null)
                            ->isObjectNew(true);

                        $attribute->setUserType($type)
                            ->setResourceId($resourceId)
                            ->setOperation($operation)
                            ->setAllowedAttributes(implode(',', array_keys($attributes)))
                            ->save();
                    }
                }
            }

            $session->addSuccess($this->__('The attribute rules were saved.'));
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while saving attribute rules.'));
        }

        $this->_redirect('*/*/edit', ['type' => $type]);
    }
}
