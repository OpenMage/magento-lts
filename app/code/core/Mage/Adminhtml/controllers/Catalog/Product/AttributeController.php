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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product attribute controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Catalog_Product_AttributeController extends Mage_Adminhtml_Controller_Action
{

    protected $_entityTypeId;

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
    }

    protected function _initAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Attributes'));

        if($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $this->loadLayout()
                ->_setActiveMenu('catalog/attributes')
                ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
                ->_addBreadcrumb(Mage::helper('catalog')->__('Manage Product Attributes'), Mage::helper('catalog')->__('Manage Product Attributes'))
            ;
        }
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        $model = Mage::getModel('catalog/resource_eav_attribute')
            ->setEntityTypeId($this->_entityTypeId);

        if ($id) {
            $model->load($id);

            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This attribute no longer exists'));
                $this->_redirect('*/*/');
                return;
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This attribute cannot be edited.'));
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

        $this->_addBreadcrumb($id ? Mage::helper('catalog')->__('Edit Product Attribute') : Mage::helper('catalog')->__('New Product Attribute'), $id ? Mage::helper('catalog')->__('Edit Product Attribute') : Mage::helper('catalog')->__('New Product Attribute'))
             ->_addContent($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit')->setData('action', $this->getUrl('*/catalog_product_attribute/save')))
             ->_addLeft($this->getLayout()->createBlock('adminhtml/catalog_product_attribute_edit_tabs'))
             ->_addJs(
                $this->getLayout()->createBlock('adminhtml/template')
                    ->setIsPopup((bool)$this->getRequest()->getParam('popup'))
                    ->setTemplate('catalog/product/attribute/js.phtml')
            )
            ->renderLayout();
    }

    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->getRequest()->getParam('attribute_code');
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $redirectBack   = $this->getRequest()->getParam('back', false);
            $model = Mage::getModel('catalog/resource_eav_attribute');
            /* @var $model Mage_Catalog_Model_Entity_Attribute */

            if ($id = $this->getRequest()->getParam('attribute_id')) {

                $model->load($id);

                if (!$model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This Attribute no longer exists'));
                    $this->_redirect('*/*/');
                    return;
                }

                // entity type check
                if ($model->getEntityTypeId() != $this->_entityTypeId) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This attribute cannot be updated.'));
                    Mage::getSingleton('adminhtml/session')->setAttributeData($data);
                    $this->_redirect('*/*/');
                    return;
                }

                $data['attribute_code'] = $model->getAttributeCode();
                $data['is_user_defined'] = $model->getIsUserDefined();
                $data['frontend_input'] = $model->getFrontendInput();
            }

            if (!isset($data['is_configurable'])) {
                $data['is_configurable'] = 0;
            }
            if (!isset($data['is_filterable'])) {
                $data['is_filterable'] = 0;
            }
            if (!isset($data['is_filterable_in_search'])) {
                $data['is_filterable_in_search'] = 0;
            }

            if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
                $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);
            }

            $defaultValueField = $model->getDefaultValueByInput($data['frontend_input']);
            if ($defaultValueField) {
                $data['default_value'] = $this->getRequest()->getParam($defaultValueField);
            }

            if(!isset($data['apply_to'])) {
                $data['apply_to'] = array();
            }

            /**
             * @todo need specify relations for properties
             */
            if (isset($data['frontend_input']) && $data['frontend_input'] == 'multiselect') {
                $data['backend_model'] = 'eav/entity_attribute_backend_array';
            }

            $model->addData($data);


            if (!$id) {
                $model->setEntityTypeId($this->_entityTypeId);
                $model->setIsUserDefined(1);
            }


            if($this->getRequest()->getParam('set') && $this->getRequest()->getParam('group')) {
                // For creating product attribute on product page we need specify attribute set and group
                $model->setAttributeSetId($this->getRequest()->getParam('set'));
                $model->setAttributeGroupId($this->getRequest()->getParam('group'));
            }

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The product attribute has been saved.'));

                /**
                 * Clear translation cache because attribute labels are stored in translation
                 */
                Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
                Mage::getSingleton('adminhtml/session')->setAttributeData(false);
                if ($this->getRequest()->getParam('popup')) {
                    $this->_redirect('adminhtml/catalog_product/addAttribute', array(
                        'id'       => $this->getRequest()->getParam('product'),
                        'attribute'=> $model->getId(),
                        '_current' => true
                    ));
                } elseif ($redirectBack) {
                    $this->_redirect('*/*/edit', array('attribute_id' => $model->getId(),'_current'=>true));
                } else {
                    $this->_redirect('*/*/', array());
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAttributeData($data);
                $this->_redirect('*/*/edit', array('_current' => true));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('attribute_id')) {
            $model = Mage::getModel('catalog/resource_eav_attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('This attribute cannot be deleted.'));
                $this->_redirect('*/*/');
                return;
            }

            try {
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('The product attribute has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('attribute_id' => $this->getRequest()->getParam('attribute_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Unable to find an attribute to delete.'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes');
    }
}
