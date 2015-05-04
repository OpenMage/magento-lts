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
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * GoogleBase Admin Item Types Controller
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @name       Mage_GoogleBase_Adminhtml_Googlebase_TypesController
 * @author     Magento Core Team <core@magentocommerce.com>
*/
class Mage_GoogleBase_Adminhtml_Googlebase_TypesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Dispatches controller_action_postdispatch_adminhtml Event (as not Adminhtml router)
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if ($this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
            return;
        }
        Mage::dispatchEvent('controller_action_postdispatch_adminhtml', array('controller_action' => $this));
    }

    protected function _initItemType()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Google Base'))
             ->_title($this->__('Manage Attributes'));

        Mage::register('current_item_type', Mage::getModel('googlebase/type'));
        $typeId = $this->getRequest()->getParam('id');
        if (!is_null($typeId)) {
            Mage::registry('current_item_type')->load($typeId);
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('catalog/googlebase/types')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog'), Mage::helper('adminhtml')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Google Base'), Mage::helper('adminhtml')->__('Google Base'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Google base'))
             ->_title($this->__('Manage Attributes'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('googlebase')->__('Item Types'), Mage::helper('googlebase')->__('Item Types'))
            ->_addContent($this->getLayout()->createBlock('googlebase/adminhtml_types'))
            ->renderLayout();
    }

    /**
     * Grid for AJAX request
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('googlebase/adminhtml_types_grid')->toHtml()
        );
    }

    public function newAction()
    {
        try {
            $this->_initItemType();

            $this->_title($this->__('New ItemType'));

            $this->_initAction()
                ->_addBreadcrumb(Mage::helper('googlebase')->__('New Item Type'), Mage::helper('adminhtml')->__('New Item Type'))
                ->_addContent($this->getLayout()->createBlock('googlebase/adminhtml_types_edit'))
                ->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
        }
    }

    public function editAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Google base'))
             ->_title($this->__('Manage Attributes'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('googlebase/type');

        try {
            $result = array();
            if ($id) {
                $model->load($id);
                $collection = Mage::getResourceModel('googlebase/attribute_collection')
                    ->addTypeFilter($model->getTypeId())
                    ->load();
                foreach ($collection as $attribute) {
                    $result[] = $attribute->getData();
                }
            }

            $this->_title($this->__('Edit Item Type'));

            Mage::register('current_item_type', $model);
            Mage::register('attributes', $result);

            $this->_initAction()
                ->_addBreadcrumb($id ? Mage::helper('googlebase')->__('Edit Item Type') : Mage::helper('googlebase')->__('New Item Type'), $id ? Mage::helper('googlebase')->__('Edit Item Type') : Mage::helper('googlebase')->__('New Item Type'))
                ->_addContent($this->getLayout()->createBlock('googlebase/adminhtml_types_edit'))
                ->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
    }

    public function saveAction()
    {
        $typeModel = Mage::getModel('googlebase/type');
        $id = $this->getRequest()->getParam('type_id');
        if (!is_null($id)) {
            $typeModel->load($id);
        }

        try {
            if ($typeModel->getId()) {
                $collection = Mage::getResourceModel('googlebase/attribute_collection')
                    ->addTypeFilter($typeModel->getId())
                    ->load();
                foreach ($collection as $attribute) {
                    $attribute->delete();
                }
            }
            $typeModel->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'))
                ->setGbaseItemtype($this->getRequest()->getParam('gbase_itemtype'))
                ->setTargetCountry($this->getRequest()->getParam('target_country'))
                ->save();


            $attributes = $this->getRequest()->getParam('attributes');
            if (is_array($attributes)) {
                $typeId = $typeModel->getId();
                foreach ($attributes as $attrInfo) {
                    if (isset($attrInfo['delete']) && $attrInfo['delete'] == 1) {
                        continue;
                    }
                    Mage::getModel('googlebase/attribute')
                        ->setAttributeId($attrInfo['attribute_id'])
                        ->setGbaseAttribute($attrInfo['gbase_attribute'])
                        ->setTypeId($typeId)
                        ->save();
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('googlebase')->__('The item type has been saved.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
    }

    public function deleteAction ()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('googlebase/type');
            $model->load($id);
            if ($model->getTypeId()) {
                $model->delete();
            }
            $this->_getSession()->addSuccess($this->__('Item Type was deleted'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/index', array('store' => $this->_getStore()->getId()));
    }

    public function loadAttributesAction ()
    {
        try {
            $this->getResponse()->setBody(
            $this->getLayout()->createBlock('googlebase/adminhtml_types_edit_attributes')
                ->setAttributeSetId($this->getRequest()->getParam('attribute_set_id'))
                ->setGbaseItemtype($this->getRequest()->getParam('gbase_itemtype'))
                ->setTargetCountry($this->getRequest()->getParam('target_country'))
                ->setAttributeSetSelected(true)
                ->toHtml()
            );
        } catch (Exception $e) {
            // just need to output text with error
            $this->_getSession()->addError($e->getMessage());
        }
    }

    public function loadItemTypesAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->getBlockSingleton('googlebase/adminhtml_types_edit_form')
                    ->getItemTypesSelectElement($this->getRequest()->getParam('target_country'))
                    ->toHtml()
            );
        } catch (Exception $e) {
            // just need to output text with error
            $this->_getSession()->addError($e->getMessage());
        }
    }

    protected function loadAttributeSetsAction()
    {
        try {
            $this->getResponse()->setBody(
                $this->getLayout()->getBlockSingleton('googlebase/adminhtml_types_edit_form')
                    ->getAttributeSetsSelectElement($this->getRequest()->getParam('target_country'))
                    ->toHtml()
            );
        } catch (Exception $e) {
            // just need to output text with error
            $this->_getSession()->addError($e->getMessage());
        }
    }

    public function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        if ($storeId == 0) {
            return Mage::app()->getAnyStoreView();
        }
        return Mage::app()->getStore($storeId);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/googlebase/types');
    }
}
