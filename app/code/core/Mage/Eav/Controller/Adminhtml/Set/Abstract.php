<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Attribute set controller
 *
 * @category   Mage
 * @package    Mage_Eav
 */
abstract class Mage_Eav_Controller_Adminhtml_Set_Abstract extends Mage_Adminhtml_Controller_Action
{
    protected string $_entityCode;

    protected Mage_Eav_Model_Entity_Type $_entityType;

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     * @throws Mage_Core_Exception
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
             ->_addContent($this->getLayout()->createBlock('eav/adminhtml_attribute_set'))
             ->renderLayout();
    }

    public function editAction()
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        if (!$attributeSet->getId()) {
            $this->_redirect('*/*/index');
            return;
        }

        Mage::register('current_attribute_set', $attributeSet);

        $this->_initAction()
             ->_title($attributeSet->getAttributeSetName())
             ->_addContent($this->getLayout()->createBlock('eav/adminhtml_attribute_set_main'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function setGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('eav/adminhtml_attribute_set_grid')
                ->toHtml()
        );
    }

    /**
     * Save attribute set action
     *
     * [POST] Create attribute set from another set and redirect to edit page
     * [AJAX] Save attribute set data
     *
     */
    public function saveAction()
    {
        $entityTypeId   = $this->_entityType->getEntityTypeId();
        $hasError       = false;
        $attributeSetId = $this->getRequest()->getParam('id', false);
        $isNewSet       = $this->getRequest()->getParam('gotoEdit', false) == '1';

        /** @var Mage_Eav_Model_Entity_Attribute_Set $model */
        $model  = Mage::getModel('eav/entity_attribute_set')
                ->setEntityTypeId($entityTypeId);

        /** @var Mage_Eav_Helper_Data $helper */
        $helper = Mage::helper('eav');

        try {
            if ($isNewSet) {
                //filter html tags
                $name = $helper->stripTags($this->getRequest()->getParam('attribute_set_name'));
                $model->setAttributeSetName(trim($name));
            } else {
                if ($attributeSetId) {
                    $model->load($attributeSetId);
                }
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('eav')->__('This attribute set no longer exists.'));
                }
                $data = Mage::helper('core')->jsonDecode($this->getRequest()->getPost('data'));

                //filter html tags
                $data['attribute_set_name'] = $helper->stripTags($data['attribute_set_name']);

                $model->organizeData($data);
            }

            $model->validate();
            if ($isNewSet) {
                $model->save();
                $model->initFromSkeleton($this->getRequest()->getParam('skeleton_set'));
            }
            $model->save();
            $this->_getSession()->addSuccess(Mage::helper('eav')->__('The attribute set has been saved.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $hasError = true;
        } catch (Throwable $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('eav')->__('An error occurred while saving the attribute set.')
            );
            $hasError = true;
        }

        if ($isNewSet) {
            if ($hasError) {
                $this->_redirect('*/*/add');
            } else {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
            }
        } else {
            $response = [];
            if ($hasError) {
                $this->_initLayoutMessages('adminhtml/session');
                $response['error']   = 1;
                $response['message'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
            } else {
                $response['error']   = 0;
                $response['url']     = $this->getUrl('*/*/');
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }

    public function addAction()
    {
        $this->_initAction()
             ->_title($this->__('New Set'))
             ->_addContent($this->getLayout()->createBlock('eav/adminhtml_attribute_set_toolbar_add'))
             ->renderLayout();
    }

    public function deleteAction()
    {
        $setId = $this->getRequest()->getParam('id');
        try {
            Mage::getModel('eav/entity_attribute_set')
                ->setId($setId)
                ->delete();

            $this->_getSession()->addSuccess($this->__('The attribute set has been removed.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred while deleting this set.'));
            $this->_redirectReferer();
        }
    }
}
