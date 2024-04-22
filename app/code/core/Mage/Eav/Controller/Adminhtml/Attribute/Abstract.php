<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Attribute controller
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_Eav_Controller_Adminhtml_Attribute_Abstract extends Mage_Adminhtml_Controller_Action
{
    /** @var string $_entityCode */
    protected $_entityCode;

    /** @var Mage_Eav_Model_Entity_Type $_entityType */
    protected $_entityType;

    /**
     * Controller predispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        $this->_entityType = Mage::getModel('eav/entity')->setType($this->_entityCode)->getEntityType();
        if (!Mage::registry('entity_type')) {
            Mage::register('entity_type', $this->_entityType);
        }
        return parent::preDispatch();
    }

    protected function _initAction()
    {
        return $this->loadLayout();
    }

    public function indexAction()
    {
        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('eav/adminhtml_attribute'))
             ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $model = Mage::getModel($this->_entityType->getAttributeModel())
               ->setEntityTypeId($this->_entityType->getEntityTypeId());
        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('eav')->__('This attribute no longer exists')
                );
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityType->getEntityTypeId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('eav')->__('This attribute cannot be edited.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getAttributeData(true);
        if (! empty($data)) {
            $model->addData($data);
        }

        Mage::register('entity_attribute', $model);

        $this->_initAction();

        $this->_title($id ? $model->getName() : $this->__('New Attribute'));

        $item = $id ? Mage::helper('eav')->__('Edit Attribute')
                    : Mage::helper('eav')->__('New Attribute');

        $this->_addBreadcrumb($item, $item);

        $this->_addLeft($this->getLayout()->createBlock('eav/adminhtml_attribute_edit_tabs'))
             ->_addContent($this->getLayout()->createBlock('eav/adminhtml_attribute_edit'));

        $this->_addJs(
            $this->getLayout()->createBlock('adminhtml/template')
                 ->setTemplate('eav/attribute/js.phtml')
        );

        $this->renderLayout();
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        $attribute = Mage::getModel($this->_entityType->getAttributeModel());
        $attribute->loadByCode($this->_entityType->getEntityTypeId(), $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('eav')->__('Attribute with the same code already exists')
            );
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @return array
     */
    protected function _filterPostData($data)
    {
        if ($data) {
            //labels
            $data['frontend_label'] = (array) $data['frontend_label'];
            foreach ($data['frontend_label'] as & $value) {
                if ($value) {
                    $value = Mage::helper('eav')->stripTags($value);
                }
            }

            if (!empty($data['option']) && !empty($data['option']['value']) && is_array($data['option']['value'])) {
                foreach ($data['option']['value'] as $key => $values) {
                    foreach ($values as $storeId => $storeLabel) {
                        $data['option']['value'][$key][$storeId] = Mage::helper('eav')->stripTags($storeLabel);
                    }
                }
            }
        }
        return $data;
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            /** @var Mage_Admin_Model_Session $session */
            $session = Mage::getSingleton('adminhtml/session');

            $redirectBack   = $this->getRequest()->getParam('back', false);
            /** @var Mage_Eav_Model_Entity_Attribute $model */
            $model = Mage::getModel($this->_entityType->getAttributeModel());
            /** @var Mage_Eav_Helper_Data $helper */
            $helper = Mage::helper('eav');

            $id = $this->getRequest()->getParam('attribute_id');

            //validate attribute_code
            if (isset($data['attribute_code'])) {
                $validatorAttrCode = new Zend_Validate_Regex(array('pattern' => '/^(?!event$)[a-z][a-z_0-9]{1,254}$/'));
                if (!$validatorAttrCode->isValid($data['attribute_code'])) {
                    $session->addError(
                        Mage::helper('eav')->__('Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter. Do not use "event" for an attribute code.')
                    );
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }


            //validate frontend_input
            if (isset($data['frontend_input'])) {
                /** @var Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator $validatorInputType */
                $validatorInputType = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
                if (!$validatorInputType->isValid($data['frontend_input'])) {
                    foreach ($validatorInputType->getMessages() as $message) {
                        $session->addError($message);
                    }
                    $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                    return;
                }
            }

            if ($id) {
                $model->load($id);

                if (!$model->getId()) {
                    $session->addError(
                        Mage::helper('eav')->__('This Attribute no longer exists')
                    );
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityType->getEntityTypeId()) {
                    $session->addError(
                        Mage::helper('eav')->__('This attribute cannot be updated.')
                    );
                    $session->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['backend_model'] = $model->getBackendModel();
                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            } else {
                /**
                * @todo add to helper and specify all relations for properties
                */
                $data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
                $data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if ($model) {
                $data['entity_type_id'] = $model->getEntityTypeId();
            }

            //filter
            $data = $this->_filterPostData($data);
            $model->addData($data);

            if (!$id) {
                $model->setEntityTypeId($this->_entityType->getEntityTypeId());
                $model->setIsUserDefined(1);
            }


            if ($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating attribute on page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }

            Mage::dispatchEvent(
                "adminhtml_{$this->_entityCode}_attribute_edit_prepare_save",
                array('object' => $model, 'request' => $this->getRequest())
            );

            try {
                $model->save();
                $session->addSuccess(
                    Mage::helper('eav')->__('The attribute has been saved.')
                );

                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                $session->setAttributeData(false);
                if ($redirectBack) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current' => true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                $session->addError($e->getMessage());
                $session->setAttributeData($data);
                $this->_redirect('*/*/edit', array('attribute_id' => $id, '_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel($this->_entityType->getAttributeModel());

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityType->getEntityTypeId() || !$model->getIsUserDefined()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('eav')->__('This attribute cannot be deleted.')
                );
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('eav')->__('The attribute has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('eav')->__('Unable to find an attribute to delete.')
        );
        $this->_redirect('*/*/');
    }
}
