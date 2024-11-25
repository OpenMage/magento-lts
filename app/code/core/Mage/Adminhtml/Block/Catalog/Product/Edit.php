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
 * Customer edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit.phtml');
        $this->setId('product_edit');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild(
                'back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('catalog')->__('Back'),
                        'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/', ['store' => $this->getRequest()->getParam('store', 0)])),
                        'class'     => 'back',
                    ]),
            );
        } else {
            $this->setChild(
                'back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('catalog')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class'     => 'cancel',
                    ]),
            );
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->setChild(
                'reset_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('catalog')->__('Reset'),
                        'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/*', ['_current' => true])),
                    ]),
            );

            $this->setChild(
                'save_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('catalog')->__('Save'),
                        'onclick'   => 'productForm.submit()',
                        'class'     => 'save',
                    ]),
            );
        }

        if (!$this->getRequest()->getParam('popup')) {
            if (!$this->getProduct()->isReadonly()) {
                $this->setChild(
                    'save_and_edit_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData([
                            'label'     => Mage::helper('catalog')->__('Save and Continue Edit'),
                            'onclick'   => Mage::helper('core/js')->getSaveAndContinueEditJs($this->getSaveAndContinueUrl()),
                            'class'     => 'save',
                        ]),
                );
            }
            if ($this->getProduct()->isDeleteable()) {
                $this->setChild(
                    'delete_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData([
                            'label'     => Mage::helper('catalog')->__('Delete'),
                            'onclick'   => Mage::helper('core/js')->getConfirmSetLocationJs($this->getDeleteUrl()),
                            'class'     => 'delete',
                        ]),
                );
            }

            if ($this->getProduct()->isDuplicable()) {
                $this->setChild(
                    'duplicate_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData([
                        'label'     => Mage::helper('catalog')->__('Duplicate'),
                        'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getDuplicateUrl()),
                        'class'     => 'add',
                    ]),
                );
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * @return string
     */
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * @return string
     */
    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * @return string
     */
    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => null]);
    }

    /**
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', [
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null,
        ]);
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }

    public function getIsGrouped()
    {
        return $this->getProduct()->isGrouped();
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrlSecure('*/*/delete', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        $header = '';
        if ($this->getProduct()->getId()) {
            $header = $this->escapeHtml($this->getProduct()->getName());
        } else {
            $header = Mage::helper('catalog')->__('New Product');
        }
        if ($setName = $this->getAttributeSetName()) {
            $header .= ' (' . $setName . ')';
        }
        return $header;
    }

    /**
     * @return string
     */
    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('eav/entity_attribute_set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    /**
     * @return bool
     */
    public function getIsConfigured()
    {
        $superAttributes = true;
        $product = $this->getProduct();
        if ($product->isConfigurable()) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $productType */
            $productType = $product->getTypeInstance(true);
            $superAttributes = $productType->getUsedProductAttributeIds($product);
            if (!$superAttributes) {
                $superAttributes = false;
            }
        }

        return !$product->isConfigurable() || $superAttributes !== false;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab', '')));
    }
}
