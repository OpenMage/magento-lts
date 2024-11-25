<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Widget
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admihtml Manage Widgets Instance Controller
 *
 * @category   Mage
 * @package    Mage_Widget
 */
class Mage_Widget_Adminhtml_Widget_InstanceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/widget_instance';

    /**
     * Session getter
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/widget_instance')
            ->_addBreadcrumb(
                Mage::helper('widget')->__('CMS'),
                Mage::helper('widget')->__('CMS'),
            )
            ->_addBreadcrumb(
                Mage::helper('widget')->__('Manage Widget Instances'),
                Mage::helper('widget')->__('Manage Widget Instances'),
            );
        return $this;
    }

    /**
     * Init widget instance object and set it to registry
     *
     * @return Mage_Widget_Model_Widget_Instance|bool
     * @throws Mage_Core_Exception
     */
    protected function _initWidgetInstance()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Widgets'));

        /** @var Mage_Widget_Model_Widget_Instance $widgetInstance */
        $widgetInstance = Mage::getModel('widget/widget_instance');

        $instanceId = $this->getRequest()->getParam('instance_id', null);
        $type       = $this->getRequest()->getParam('type', null);
        $package    = $this->getRequest()->getParam('package', null);
        $theme      = $this->getRequest()->getParam('theme', null);

        if ($instanceId) {
            $widgetInstance->load($instanceId);
            if (!$widgetInstance->getId()) {
                $this->_getSession()->addError(Mage::helper('widget')->__('Wrong widget instance specified.'));
                return false;
            }
        } else {
            $packageTheme = $package . '/' . $theme == '/' ? null : $package . '/' . $theme;
            $widgetInstance->setType($type)
                ->setPackageTheme($packageTheme);
        }
        Mage::register('current_widget_instance', $widgetInstance);
        return $widgetInstance;
    }

    /**
     * Widget Instances Grid
     */
    public function indexAction()
    {
        $this->_title($this->__('CMS'))->_title($this->__('Widgets'));

        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New widget instance action (forward to edit action)
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit widget instance action
     */
    public function editAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance) {
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($widgetInstance->getId() ? $widgetInstance->getTitle() : $this->__('New Instance'));

        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Set body to response
     *
     * @param string $body
     */
    private function setBody($body)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($body);
        $this->getResponse()->setBody($body);
    }

    /**
     * Validate action
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $widgetInstance = $this->_initWidgetInstance();
        $result = $widgetInstance->validate();
        if ($result !== true && is_string($result)) {
            $this->_getSession()->addError($result);
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }
        $this->setBody($response->toJson());
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if (!$widgetInstance || !$this->_validatePostData($widgetInstance, $this->getRequest()->getPost())) {
            $this->_redirect('*/*/');
            return;
        }
        $widgetInstance->setTitle($this->getRequest()->getPost('title'))
            ->setStoreIds($this->getRequest()->getPost('store_ids', [0]))
            ->setSortOrder($this->getRequest()->getPost('sort_order', 0))
            ->setPageGroups($this->getRequest()->getPost('widget_instance'))
            ->setWidgetParameters($this->_prepareParameters());
        try {
            $widgetInstance->save();
            $this->_getSession()->addSuccess(
                Mage::helper('widget')->__('The widget instance has been saved.'),
            );
            if ($this->getRequest()->getParam('back', false)) {
                $this->_redirect('*/*/edit', [
                    'instance_id' => $widgetInstance->getId(),
                    '_current' => true,
                ]);
            } else {
                $this->_redirect('*/*/');
            }
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error occurred during saving a widget: %s', $e->getMessage()));
        }
        $this->_redirect('*/*/edit', ['_current' => true]);
    }

    /**
     * Delete Action
     * @throws Mage_Core_Exception|Throwable
     */
    public function deleteAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        if ($widgetInstance) {
            try {
                $widgetInstance->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('widget')->__('The widget instance has been deleted.'),
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Categories chooser Action (Ajax request)
     *
     */
    public function categoriesAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $isAnchorOnly = $this->getRequest()->getParam('is_anchor_only', 0);
        $chooser = $this->getLayout()
            ->createBlock('adminhtml/catalog_category_widget_chooser')
            ->setUseMassaction(true)
            ->setId(Mage::helper('core')->uniqHash('categories'))
            ->setIsAnchorOnly($isAnchorOnly)
            ->setSelectedCategories(explode(',', $selected));
        $this->setBody($chooser->toHtml());
    }

    /**
     * Products chooser Action (Ajax request)
     */
    public function productsAction()
    {
        $selected = $this->getRequest()->getParam('selected', '');
        $productTypeId = $this->getRequest()->getParam('product_type_id', '');
        $chooser = $this->getLayout()
            ->createBlock('adminhtml/catalog_product_widget_chooser')
            ->setName(Mage::helper('core')->uniqHash('products_grid_'))
            ->setUseMassaction(true)
            ->setProductTypeId($productTypeId)
            ->setSelectedProducts(explode(',', $selected));
        /** @var Mage_Adminhtml_Block_Widget_Grid_Serializer $serializer */
        $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serializer->initSerializerBlock($chooser, 'getSelectedProducts', 'selected_products', 'selected_products');
        $this->setBody($chooser->toHtml() . $serializer->toHtml());
    }

    /**
     * Blocks Action (Ajax request)
     */
    public function blocksAction()
    {
        /** @var Mage_Widget_Model_Widget_Instance $widgetInstance */
        $widgetInstance = $this->_initWidgetInstance();
        $layout = $this->getRequest()->getParam('layout');
        $selected = $this->getRequest()->getParam('selected', null);
        $blocksChooser = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_chooser_block')
            ->setArea($widgetInstance->getArea())
            ->setPackage($widgetInstance->getPackage())
            ->setTheme($widgetInstance->getTheme())
            ->setLayoutHandle($layout)
            ->setSelected($selected)
            ->setAllowedBlocks($widgetInstance->getWidgetSupportedBlocks());
        $this->setBody($blocksChooser->toHtml());
    }

    /**
     * Templates Chooser Action (Ajax request)
     */
    public function templateAction()
    {
        /** @var Mage_Widget_Model_Widget_Instance $widgetInstance */
        $widgetInstance = $this->_initWidgetInstance();
        $block = $this->getRequest()->getParam('block');
        $selected = $this->getRequest()->getParam('selected', null);
        $templateChooser = $this->getLayout()
            ->createBlock('widget/adminhtml_widget_instance_edit_chooser_template')
            ->setSelected($selected)
            ->setWidgetTemplates($widgetInstance->getWidgetSupportedTemplatesByBlock($block));
        $this->setBody($templateChooser->toHtml());
    }

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

    /**
     * Prepare widget parameters
     *
     * @return array
     */
    protected function _prepareParameters()
    {
        $result = [];
        $parameters = $this->getRequest()->getPost('parameters');
        if (is_array($parameters) && count($parameters)) {
            foreach ($parameters as $key => $value) {
                $result[Mage::helper('core')->stripTags($key)] = $value;
            }
        }
        return $result;
    }

    /**
     * Validates update xml post data
     *
     * @param Mage_Widget_Model_Widget_Instance $widgetInstance
     * @param array $data
     * @return bool
     */
    protected function _validatePostData($widgetInstance, $data)
    {
        $errorNo = true;
        if (!empty($data['widget_instance']) && is_array($data['widget_instance'])) {
            $validatorCustomLayout = Mage::getModel('adminhtml/layoutUpdate_validator');
            foreach ($data['widget_instance'] as $pageGroup) {
                try {
                    if (!empty($pageGroup['page_group'])
                        && !empty($pageGroup[$pageGroup['page_group']]['template'])
                        && !empty($pageGroup[$pageGroup['page_group']]['block'])
                        && !$validatorCustomLayout->isValid($widgetInstance->generateLayoutUpdateXml(
                            $pageGroup[$pageGroup['page_group']]['block'],
                            $pageGroup[$pageGroup['page_group']]['template'],
                        ))
                    ) {
                        $errorNo = false;
                    }
                } catch (Exception $exception) {
                    Mage::logException($exception);
                    $this->_getSession()->addError(
                        $this->__('An error occurred during POST data validation: %s', $exception->getMessage()),
                    );
                    $errorNo = false;
                }
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->_getSession()->addError($message);
            }
        }
        return $errorNo;
    }
}
