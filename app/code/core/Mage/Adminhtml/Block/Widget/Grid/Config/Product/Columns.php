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
    public const CONFIG_PATH_GRID_ENBALED = 'admin/grid_catalog/enabled';
    public const CONFIG_PATH_GRID_COLUMNS = 'admin/grid_catalog/columns';
    public const CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH = 'admin/grid_catalog/imagewith';

    protected $_configColumns = [];
    protected $_enabledGrids = [];

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function getGridEnabled(): array
    {
        if (empty($this->_enabledGrids)) {
            if (Mage::getStoreConfig(self::CONFIG_PATH_GRID_ENBALED)) {
                $this->_enabledGrids = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_GRID_ENBALED));
            }
        }
        return $this->_enabledGrids;
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function isGridEnabled(): bool
    {
        return in_array($this->getId(), $this->getGridEnabled());
    }

    /**
     * Get list of columns that should be showed
     *
     * @return array
     */
    public function getGridConfigColumns() : array
    {
        if (empty($this->_configColumns)) {
            if (Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMNS)) {
                $_attributeCodes = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMNS));
                foreach ($_attributeCodes as $attributeCode) {
                    $this->_configColumns[$attributeCode] = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
                }
            }
        }
        return $this->_configColumns;
    }

    /**
     * Prepare collection custom config
     *
     * @return $this
     */
    protected function _prepareCollectionFromConfig()
    {
        if (!$this->isGridEnabled()) {
            return false;
        }

        if ($this->getCollection()) {   
            foreach ($this->getGridConfigColumns() as $attributeCode => $_attributeEntity) {
                $this->getCollection()->addAttributeToSelect($attributeCode);
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
        if (!$this->isGridEnabled()) {
            return false;
        }

        $_keepOrder = 'entity_id';
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        /** @var Mage_Eav_Model_Attribute $_attributeEntity */
        foreach ($this->getGridConfigColumns() as $attributeCode => $_attributeEntity) {
            switch ($_attributeEntity->getFrontendInput()) {
                case 'media_image':
                    $this->addColumnAfter(
                        $attributeCode,
                        [
                            'header' => Mage::helper('catalog')->__($_attributeEntity->getFrontendLabel()),
                            'width' => Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH),
                            'type'  => 'productimage',
                            'index' => $attributeCode,
                            'attribute_code' => $attributeCode,
                        ],
                        $_keepOrder
                    );
                    break;
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