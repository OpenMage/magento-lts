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
 * Urlrewrites edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Set form id and title
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('urlrewrite_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Block Information'));
    }

    /**
     * Prepare the form layout
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model    = Mage::registry('current_urlrewrite');
        $product  = Mage::registry('current_product');
        $category = Mage::registry('current_category');

        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ],
        );

        // set form data either from model values or from session
        $formValues = [
            'store_id'     => $model->getStoreId(),
            'id_path'      => $model->getIdPath(),
            'request_path' => $model->getRequestPath(),
            'target_path'  => $model->getTargetPath(),
            'options'      => $model->getOptions(),
            'description'  => $model->getDescription(),
        ];
        if ($sessionData = Mage::getSingleton('adminhtml/session')->getData('urlrewrite_data', true)) {
            foreach (array_keys($formValues) as $key) {
                if (isset($sessionData[$key])) {
                    $formValues[$key] = $sessionData[$key];
                }
            }
        }

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'    => Mage::helper('adminhtml')->__('URL Rewrite Information'),
        ]);

        $fieldset->addField('is_system', 'select', [
            'label'     => Mage::helper('adminhtml')->__('Type'),
            'title'     => Mage::helper('adminhtml')->__('Type'),
            'name'      => 'is_system',
            'required'  => true,
            'options'   => [
                1 => Mage::helper('adminhtml')->__('System'),
                0 => Mage::helper('adminhtml')->__('Custom'),
            ],
            'disabled'  => true,
            'value'     => $model->getIsSystem(),
        ]);

        $isFilterAllowed = false;
        // get store switcher or a hidden field with its id
        if (!Mage::app()->isSingleStoreMode()) {
            $stores  = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm();
            $entityStores = [];
            $noStoreError = false;

            //showing websites that only associated to products
            if ($product && $product->getId()) {
                $entityStores = $product->getStoreIds() ? $product->getStoreIds() : [];
                if (!$entityStores) {
                    $stores = []; //reset the stores
                    $noStoreError = $this->__('Chosen product does not associated with any website, so url rewrite is not possible.');
                }
                //if category is chosen, reset stores which are not related with this category
                if ($category && $category->getId()) {
                    $categoryStores = $category->getStoreIds() ? $category->getStoreIds() : [];
                    $entityStores = array_intersect($entityStores, $categoryStores);
                }
                $isFilterAllowed = true;
            } elseif ($category && $category->getId()) {
                $entityStores = $category->getStoreIds() ? $category->getStoreIds() : [];
                if (!$entityStores) {
                    $stores = []; //reset the stores
                    $noStoreError = $this->__('Chosen category does not associated with any website, so url rewrite is not possible.');
                }
                $isFilterAllowed = true;
            }

            /*
             * Stores should be filtered only if product and/or category is specified.
             * If we use custom rewrite, all stores are accepted.
             */
            if ($stores && $isFilterAllowed) {
                foreach ($stores as $index => $store) {
                    if (isset($store['value']) && $store['value']) {
                        $found = false;
                        foreach ($store['value'] as $key => $value) {
                            if (isset($value['value']) && in_array($value['value'], $entityStores)) {
                                $found = true;
                            } else {
                                unset($stores[$index]['value'][$key]);
                            }
                        }
                        if (!$found) {
                            unset($stores[$index]);
                        }
                    }
                }
            }

            $element = $fieldset->addField('store_id', 'select', [
                'label'     => Mage::helper('adminhtml')->__('Store'),
                'title'     => Mage::helper('adminhtml')->__('Store'),
                'name'      => 'store_id',
                'required'  => true,
                'values'    => $stores,
                'disabled'  => true,
                'value'     => $formValues['store_id'],
            ]);
            $renderer = $this->getStoreSwitcherRenderer();
            $element->setRenderer($renderer);
            if ($noStoreError) {
                $element->setAfterElementHtml($noStoreError);
            }
            if (!$model->getIsSystem()) {
                $element->unsetData('disabled');
            }
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId(),
            ]);
        }

        $idPath = $fieldset->addField('id_path', 'text', [
            'label'     => Mage::helper('adminhtml')->__('ID Path'),
            'title'     => Mage::helper('adminhtml')->__('ID Path'),
            'name'      => 'id_path',
            'required'  => true,
            'disabled'  => true,
            'value'     => $formValues['id_path'],
        ]);

        $requestPath = $fieldset->addField('request_path', 'text', [
            'label'     => Mage::helper('adminhtml')->__('Request Path'),
            'title'     => Mage::helper('adminhtml')->__('Request Path'),
            'name'      => 'request_path',
            'required'  => true,
            'value'     => $formValues['request_path'],
        ]);

        $targetPath = $fieldset->addField('target_path', 'text', [
            'label'     => Mage::helper('adminhtml')->__('Target Path'),
            'title'     => Mage::helper('adminhtml')->__('Target Path'),
            'name'      => 'target_path',
            'required'  => true,
            'disabled'  => true,
            'value'     => $formValues['target_path'],
        ]);

        // auto-generate paths for new url rewrites
        if (!$model->getId()) {
            $newProduct  = null;
            $newCategory = null;
            if ($category->getId() || $product->getId()) {
                $newCategory = $category;
            }

            if ($product->getId()) {
                $newProduct = $product;
            }

            if ($newCategory || $newProduct) {
                $catalogUrlModel = Mage::getSingleton('catalog/url');
                $idPath->setValue($catalogUrlModel->generatePath('id', $newProduct, $newCategory));
                if (!isset($sessionData['request_path'])) {
                    $requestPath->setValue($catalogUrlModel->generatePath('request', $newProduct, $newCategory, ''));
                }
                $targetPath->setValue($catalogUrlModel->generatePath('target', $newProduct, $newCategory));
            } else {
                $idPath->unsetData('disabled');
                $targetPath->unsetData('disabled');
            }
        } else {
            if (!$model->getProductId() && !$model->getCategoryId()) {
                $idPath->unsetData('disabled');
                $targetPath->unsetData('disabled');
            }
        }

        $fieldset->addField('options', 'select', [
            'label'     => Mage::helper('adminhtml')->__('Redirect'),
            'title'     => Mage::helper('adminhtml')->__('Redirect'),
            'name'      => 'options',
            'options'   => [
                ''   => Mage::helper('adminhtml')->__('No'),
                'R'  => Mage::helper('adminhtml')->__('Temporary (302)'),
                'RP' => Mage::helper('adminhtml')->__('Permanent (301)'),
            ],
            'value'     => $formValues['options'],
        ]);

        $fieldset->addField('description', 'textarea', [
            'label'     => Mage::helper('adminhtml')->__('Description'),
            'title'     => Mage::helper('adminhtml')->__('Description'),
            'name'      => 'description',
            'cols'      => 20,
            'rows'      => 5,
            'value'     => $formValues['description'],
            'wrap'      => 'soft',
        ]);

        $form->setUseContainer(true);
        $form->setAction(Mage::helper('adminhtml')->getUrl('*/*/save', [
            'id'       => $model->getId(),
            'product'  => $product->getId(),
            'category' => $category->getId(),
        ]));
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
