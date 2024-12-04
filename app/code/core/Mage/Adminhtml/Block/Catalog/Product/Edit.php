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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
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
    protected $_idFieldName = 'product_edit';
    protected $_template    = 'catalog/product/edit.phtml';

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
            $this->setChild(self::BUTTON_BACK, $this->getButtonBackBlock());
        } else {
            $this->setChild(self::BUTTON_BACK, $this->getButtonBackPopupBlock());
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->setChild(self::BUTTON_RESET, $this->getButtonResetBlock());
            $this->setChild(self::BUTTON_SAVE, $this->getButtonSaveBlock());
        }

        if (!$this->getRequest()->getParam('popup')) {
            if (!$this->getProduct()->isReadonly()) {
                $this->setChild(self::BUTTON_SAVE_AND_CONTINUE, $this->getButtonSaveAndContinueBlock());
            }
            if ($this->getProduct()->isDeleteable()) {
                $this->setChild(self::BUTTON_DELETE, $this->getButtonDeleteBlock());
            }

            if ($this->getProduct()->isDuplicable()) {
                $this->setChild(self::BUTTON_DUPLICATE, $this->getButtonDuplicateBlock());
            }
        }

        return parent::_prepareLayout();
    }

    public function getButtonBackBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackBlock($name, $attributes)
            ->setOnClickSetLocationJsUrl('*/*/', [
                'store' => $this->getRequest()->getParam('store', 0),
            ]);
    }


    public function getButtonBackPopupBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonBackPopupBlock($name, $attributes)
            ->setLabel(Mage::helper('catalog')->__('Close Window'));
    }

    public function getButtonDeleteBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonDeleteBlock($name, $attributes)
            ->setOnClick(Mage::helper('core/js')->getConfirmSetLocationJs($this->getDeleteUrl()));
    }

    public function getButtonDuplicateBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonDuplicateBlock($name, $attributes)
            ->setOnClick(Mage::helper('core/js')->getConfirmSetLocationJs($this->getDuplicateUrl()))
            ->setClass(self::BUTTON__CLASS_ADD);
    }

    public function getButtonResetBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonResetBlock($name, $attributes)
            ->setOnClick(Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/*', ['_current' => true])))
            ->resetClass();
    }

    public function getButtonSaveBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveBlock($name, $attributes)
            ->setOnClick('productForm.submit()');
    }

    public function getButtonSaveAndContinueBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonSaveAndContinueBlock($name, $attributes)
            ->setOnClick(Mage::helper('core/js')->getSaveAndContinueEditJs($this->getSaveAndContinueUrl()));
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
            'active_tab' => null
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
