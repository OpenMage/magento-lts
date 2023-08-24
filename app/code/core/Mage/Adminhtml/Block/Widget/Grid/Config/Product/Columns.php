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
 * @copyright  Copyright (c) 2018-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid widget config columns
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 */
trait Mage_Adminhtml_Block_Widget_Grid_Config_Product_Columns
{
    /* @var Mage_Adminhtml_Helper_Widget_Grid_Config $_helper */
    private $_helperAdvancedGrid = NULL;

    protected function getHelperAdvancedGrid(): Mage_Adminhtml_Helper_Widget_Grid_Config
    {
        if (!$this->_helperAdvancedGrid) {
            $this->_helperAdvancedGrid = Mage::helper('adminhtml/widget_grid_config');
        }
        $this->_helperAdvancedGrid->setGridId($this->getId());
        return $this->_helperAdvancedGrid;
    }

    /**
     * Prepare collection custom config
     *
     * @return $this
     */
    protected function _prepareCollectionFromConfig()
    {
        if (!$this->getHelperAdvancedGrid()->isEnabled()) {
            return $this;
        }
        /** @var Mage_Core_Model_Resource_Db_Collection_Abstract $this->getCollection() */
        if ($this->getCollection()) {   
            foreach ($this->getHelperAdvancedGrid()->getColumns() as $attributeCode) {
                $this->getCollection()->addAttributeToSelect($attributeCode);
            }

            foreach ($this->getHelperAdvancedGrid()->getImageColumns() as $attributeCode) {
                $this->getCollection()->addAttributeToSelect($attributeCode);
            }

            if ($this->getHelperAdvancedGrid()->isCreatedAtEnabled()) {
                $this->getCollection()->addAttributeToSelect('created_at');
            }

            if ($this->getHelperAdvancedGrid()->isUpdatedAtEnabled()) {
                $this->getCollection()->addAttributeToSelect('updated_at');
            }
        }
        return $this;
    }

    /**
     * Prepare columns custom config
     *
     * @return $this
     */
    protected function _prepareColumnsFromConfig()
    {
        if (!$this->getHelperAdvancedGrid()->isEnabled()) {
            return $this;
        }

        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $_keepOrder = 'entity_id';

        foreach ($this->getHelperAdvancedGrid()->getImageColumns() as $attributeCode) {
            /** @var Mage_Eav_Model_Attribute $_attributeEntity */
            $_attributeEntity = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $this->addColumnAfter(
                $attributeCode,
                [
                    'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                    'width' => $this->getHelperAdvancedGrid()->getProductImageWidth(),
                    'type'  => 'productimage',
                    'index' => $attributeCode,
                    'attribute_code' => $attributeCode,
                ],
                $_keepOrder
            );
            $_keepOrder = $attributeCode;
        }

        if ($this->getHelperAdvancedGrid()->isCreatedAtEnabled()) {
            $this->addColumnAfter(
                'created_at',
                [
                    'header' => Mage::helper('catalog')->__('Created At'),
                    'type'  => 'datetime',
                    'index' => 'created_at',
                    'attribute_code' => 'created_at',
                ],
                $_keepOrder
            );
            $_keepOrder = 'created_at';
        }

        if ($this->getHelperAdvancedGrid()->isUpdatedAtEnabled()) {
            $this->addColumnAfter(
                'updated_at',
                [
                    'header' => Mage::helper('catalog')->__('Updated At'),
                    'type'  => 'datetime',
                    'index' => 'updated_at',
                    'attribute_code' => 'updated_at',
                ],
                $_keepOrder
            );
            $_keepOrder = 'updated_at';
        }

        foreach ($this->getHelperAdvancedGrid()->getColumns() as $attributeCode) {
            /** @var Mage_Eav_Model_Attribute $_attributeEntity */
            $_attributeEntity = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            switch ($_attributeEntity->getFrontendInput()) {
                case 'price':
                    $_currency = Mage::app()->getStore($storeId)->getBaseCurrency()->getCode();
                    $this->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'price',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                            'currency_code' => $_currency,
                        ],
                        $_keepOrder
                    );
                    break;
                case 'date':
                    $this->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'date',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepOrder
                    );
                    break;
                case 'multiselect':
                case 'select':
                    if ($_attributeEntity->usesSource()) {
                        $_options = [];
                        $_allOptions = $_attributeEntity->getSource()->getAllOptions(false, true);
                        foreach ($_allOptions as $key => $option) {
                            $_options[$option['value']] = $option['label'];
                        }
                    }
                    $this->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            /* 'width' => '150px', */
                            'type'  => 'options',
                            'index' => $attributeCode,
                            'options' => $_options,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepOrder
                    );
                    break;
                default:
                    $this->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'type'  => 'text',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepOrder
                    );
                    break;
            }
            $_keepOrder = $attributeCode;
        }
        return $this;
    }
}
