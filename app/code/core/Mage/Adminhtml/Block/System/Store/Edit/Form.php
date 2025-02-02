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
 * Adminhtml store edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Store_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('coreStoreForm');
    }

    /**
     * Prepare form data
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $showWebsiteFieldset    = false;
        $showGroupFieldset      = false;
        $showStoreFieldset      = false;
        $websiteModel           = null;
        $groupModel             = null;
        $storeModel             = null;

        if (Mage::registry('store_type') == 'website') {
            $websiteModel = Mage::registry('store_data');
            $showWebsiteFieldset = true;
        } elseif (Mage::registry('store_type') == 'group') {
            $groupModel = Mage::registry('store_data');
            $showGroupFieldset = true;
        } elseif (Mage::registry('store_type') == 'store') {
            $storeModel = Mage::registry('store_data');
            $showStoreFieldset = true;
        }

        /** @var Mage_Core_Model_Website $websiteModel */
        /** @var Mage_Core_Model_Store_Group $groupModel */
        /** @var Mage_Core_Model_Store $storeModel */

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ]);

        if ($showWebsiteFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $websiteModel->setData($postData['website']);
            }
            $fieldset = $form->addFieldset('website_fieldset', [
                'legend' => Mage::helper('core')->__('Website Information'),
            ]);
            /** @var Varien_Data_Form $fieldset */

            $fieldset->addField('website_name', 'text', [
                'name'      => 'website[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $websiteModel->getName(),
                'required'  => true,
                'disabled'  => $websiteModel->isReadOnly(),
            ]);

            $fieldset->addField('website_code', 'text', [
                'name'      => 'website[code]',
                'label'     => Mage::helper('core')->__('Code'),
                'value'     => $websiteModel->getCode(),
                'required'  => true,
                'disabled'  => $websiteModel->isReadOnly(),
            ]);

            $fieldset->addField('website_sort_order', 'text', [
                'name'      => 'website[sort_order]',
                'label'     => Mage::helper('core')->__('Sort Order'),
                'value'     => $websiteModel->getSortOrder(),
                'required'  => false,
                'disabled'  => $websiteModel->isReadOnly(),
            ]);

            if (Mage::registry('store_action') == 'edit') {
                $groups = Mage::getModel('core/store_group')->getCollection()
                    ->addWebsiteFilter($websiteModel->getId())
                    ->setWithoutStoreViewFilter()
                    ->toOptionArray();

                $fieldset->addField('website_default_group_id', 'select', [
                    'name'      => 'website[default_group_id]',
                    'label'     => Mage::helper('core')->__('Default Store'),
                    'value'     => $websiteModel->getDefaultGroupId(),
                    'values'    => $groups,
                    'required'  => false,
                    'disabled'  => $websiteModel->isReadOnly(),
                ]);
            }

            if (!$websiteModel->getIsDefault() && $websiteModel->getStoresCount()) {
                $fieldset->addField('is_default', 'checkbox', [
                    'name'      => 'website[is_default]',
                    'label'     => Mage::helper('core')->__('Set as Default'),
                    'value'     => 1,
                    'disabled'  => $websiteModel->isReadOnly(),
                ]);
            } else {
                $fieldset->addField('is_default', 'hidden', [
                    'name'      => 'website[is_default]',
                    'value'     => $websiteModel->getIsDefault(),
                ]);
            }

            $fieldset->addField('website_website_id', 'hidden', [
                'name'  => 'website[website_id]',
                'value' => $websiteModel->getId(),
            ]);
        }

        if ($showGroupFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $groupModel->setData($postData['group']);
            }
            $fieldset = $form->addFieldset('group_fieldset', [
                'legend' => Mage::helper('core')->__('Store Information'),
            ]);

            if (Mage::registry('store_action') == 'edit'
                || (Mage::registry('store_action') == 'add' && Mage::registry('store_type') == 'group')
            ) {
                $websites = Mage::getModel('core/website')->getCollection()->toOptionArray();
                $fieldset->addField('group_website_id', 'select', [
                    'name'      => 'group[website_id]',
                    'label'     => Mage::helper('core')->__('Website'),
                    'value'     => $groupModel->getWebsiteId(),
                    'values'    => $websites,
                    'required'  => true,
                    'disabled'  => $groupModel->isReadOnly(),
                ]);

                if ($groupModel->getId() && $groupModel->getWebsite()->getDefaultGroupId() == $groupModel->getId()) {
                    if ($groupModel->getWebsite()->getIsDefault() || $groupModel->getWebsite()->getGroupsCount() == 1) {
                        $form->getElement('group_website_id')->setDisabled(true);

                        $fieldset->addField('group_hidden_website_id', 'hidden', [
                            'name'      => 'group[website_id]',
                            'no_span'   => true,
                            'value'     => $groupModel->getWebsiteId(),
                        ]);
                    } else {
                        $fieldset->addField('group_original_website_id', 'hidden', [
                            'name'      => 'group[original_website_id]',
                            'no_span'   => true,
                            'value'     => $groupModel->getWebsiteId(),
                        ]);
                    }
                }
            }

            $fieldset->addField('group_name', 'text', [
                'name'      => 'group[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $groupModel->getName(),
                'required'  => true,
                'disabled'  => $groupModel->isReadOnly(),
            ]);

            $categories = Mage::getModel('adminhtml/system_config_source_category')->toOptionArray();

            $fieldset->addField('group_root_category_id', 'select', [
                'name'      => 'group[root_category_id]',
                'label'     => Mage::helper('core')->__('Root Category'),
                'value'     => $groupModel->getRootCategoryId(),
                'values'    => $categories,
                'required'  => true,
                'disabled'  => $groupModel->isReadOnly(),
            ]);

            if (Mage::registry('store_action') == 'edit') {
                $stores = Mage::getModel('core/store')->getCollection()
                     ->addGroupFilter($groupModel->getId())->toOptionArray();
                //array_unshift($stores, array('label'=>'', 'value'=>0));
                $fieldset->addField('group_default_store_id', 'select', [
                    'name'      => 'group[default_store_id]',
                    'label'     => Mage::helper('core')->__('Default Store View'),
                    'value'     => $groupModel->getDefaultStoreId(),
                    'values'    => $stores,
                    'required'  => false,
                    'disabled'  => $groupModel->isReadOnly(),
                ]);
            }

            $fieldset->addField('group_group_id', 'hidden', [
                'name'      => 'group[group_id]',
                'no_span'   => true,
                'value'     => $groupModel->getId(),
            ]);
        }

        if ($showStoreFieldset) {
            if ($postData = Mage::registry('store_post_data')) {
                $storeModel->setData($postData['store']);
            }
            $fieldset = $form->addFieldset('store_fieldset', [
                'legend' => Mage::helper('core')->__('Store View Information'),
            ]);

            if (Mage::registry('store_action') == 'edit'
                || Mage::registry('store_action') == 'add' && Mage::registry('store_type') == 'store'
            ) {
                $websites = Mage::getModel('core/website')->getCollection();
                $allgroups = Mage::getModel('core/store_group')->getCollection();
                $groups = [];
                foreach ($websites as $website) {
                    $values = [];
                    foreach ($allgroups as $group) {
                        if ($group->getWebsiteId() == $website->getId()) {
                            $values[] = ['label' => $group->getName(),'value' => $group->getId()];
                        }
                    }
                    $groups[] = ['label' => $this->escapeHtml($website->getName()), 'value' => $values];
                }
                $fieldset->addField('store_group_id', 'select', [
                    'name'      => 'store[group_id]',
                    'label'     => Mage::helper('core')->__('Store'),
                    'value'     => $storeModel->getGroupId(),
                    'values'    => $groups,
                    'required'  => true,
                    'disabled'  => $storeModel->isReadOnly(),
                ]);
                if ($storeModel->getId() && $storeModel->getGroup()->getDefaultStoreId() == $storeModel->getId()) {
                    if ($storeModel->getGroup() && $storeModel->getGroup()->getStoresCount() > 1) {
                        $form->getElement('store_group_id')->setDisabled(true);

                        $fieldset->addField('store_hidden_group_id', 'hidden', [
                            'name'      => 'store[group_id]',
                            'no_span'   => true,
                            'value'     => $storeModel->getGroupId(),
                        ]);
                    } else {
                        $fieldset->addField('store_original_group_id', 'hidden', [
                            'name'      => 'store[original_group_id]',
                            'no_span'   => true,
                            'value'     => $storeModel->getGroupId(),
                        ]);
                    }
                }
            }

            $fieldset->addField('store_name', 'text', [
                'name'      => 'store[name]',
                'label'     => Mage::helper('core')->__('Name'),
                'value'     => $storeModel->getName(),
                'required'  => true,
                'disabled'  => $storeModel->isReadOnly(),
            ]);
            $fieldset->addField('store_code', 'text', [
                'name'      => 'store[code]',
                'label'     => Mage::helper('core')->__('Code'),
                'value'     => $storeModel->getCode(),
                'required'  => true,
                'disabled'  => $storeModel->isReadOnly(),
            ]);

            $fieldset->addField('store_is_active', 'select', [
                'name'      => 'store[is_active]',
                'label'     => Mage::helper('core')->__('Status'),
                'value'     => $storeModel->getIsActive(),
                'options'   => [
                    0 => Mage::helper('adminhtml')->__('Disabled'),
                    1 => Mage::helper('adminhtml')->__('Enabled')],
                'required'  => true,
                'disabled'  => $storeModel->isReadOnly(),
            ]);

            $fieldset->addField('store_sort_order', 'text', [
                'name'      => 'store[sort_order]',
                'label'     => Mage::helper('core')->__('Sort Order'),
                'value'     => $storeModel->getSortOrder(),
                'required'  => false,
                'disabled'  => $storeModel->isReadOnly(),
            ]);

            $fieldset->addField('store_is_default', 'hidden', [
                'name'      => 'store[is_default]',
                'no_span'   => true,
                'value'     => $storeModel->getIsDefault(),
            ]);

            $fieldset->addField('store_store_id', 'hidden', [
                'name'      => 'store[store_id]',
                'no_span'   => true,
                'value'     => $storeModel->getId(),
                'disabled'  => $storeModel->isReadOnly(),
            ]);
        }

        $form->addField('store_type', 'hidden', [
            'name'      => 'store_type',
            'no_span'   => true,
            'value'     => Mage::registry('store_type'),
        ]);

        $form->addField('store_action', 'hidden', [
            'name'      => 'store_action',
            'no_span'   => true,
            'value'     => Mage::registry('store_action'),
        ]);

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_store_edit_form_prepare_form', ['block' => $this]);

        return parent::_prepareForm();
    }
}
