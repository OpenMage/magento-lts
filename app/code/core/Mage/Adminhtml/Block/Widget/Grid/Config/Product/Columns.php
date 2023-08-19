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
    public const CONFIG_PATH_GRID_COLUMNS = 'admin/grid_catalog/columns';
    public const CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH = 'admin/grid_catalog/imagewith';

    protected $_configColumns = [];

    /**
     * Get list of columns that should be showed
     *
     * @return array
     */
    public function getGridConfigColumns() : array
    {
        if (empty($this->_configColumns)) {
            if (Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMNS)) {
                $this->_configColumns = explode(',', Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMNS));
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
        if ($this->getCollection()) {
            foreach ($this->getGridConfigColumns() as $value) {
                switch ($value) {
                    case 'productimage':
                        $this->getCollection()->joinAttribute('image', 'catalog_product/image', 'entity_id', null, 'left');
                        break;
                }
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
        foreach ($this->getGridConfigColumns() as $value) {
            switch ($value) {
                case 'productimage':
                    $this->addColumnAfter(
                        'image',
                        [
                            'header' => Mage::helper('catalog')->__('Image'),
                            'width' => Mage::getStoreConfig(self::CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH),
                            'type'  => 'productimage',
                            'index' => 'image',
                        ],
                        'entity_id'
                    );
                    break;
            }
        }
        return $this;
    }
}
