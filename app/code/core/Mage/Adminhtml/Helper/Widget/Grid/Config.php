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
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog Grid Config Advanced Helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Widget_Grid_Config extends Mage_Core_Helper_Abstract
{
    public const CONFIG_PATH_GRID_ENABLED = 'advanced_grid/%s/enabled';
    public const CONFIG_PATH_GRID_COLUMNS = 'advanced_grid/%s/columns';
    public const CONFIG_PATH_GRID_CREATED_AT = 'advanced_grid/%s/created_at';
    public const CONFIG_PATH_GRID_UPDATED_AT = 'advanced_grid/%s/updated_at';
    public const CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH = 'advanced_grid/%s/imagewith';

    protected $_gridId = '';

    /**
     * Set grid id configuration scope
     *
     * @return $this
     */
    public function setGridId($id)
    {
        $this->_gridId = $id;
        return $this;
    }

    /**
     * Get grid id configuration scope
     *
     * @return string|null
     */
    public function getGridId(): string
    {
        return $this->_gridId;
    }

    /**
     * Get store config for grid id
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getStoreConfigGridId($configPath)
    {   if(!$this->_gridId) {
            Mage::throwException(Mage::helper('adminhtml')->__('Grid Id must be set.'));
        }
        $config = sprintf($configPath, $this->_gridId);
        return Mage::getStoreConfig($config);
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function isGridEnabled(): bool
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_ENABLED);
    }

    public function isCreatedAtEnabled()
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_CREATED_AT);
    }

    public function isUpdatedAtEnabled()
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_UPDATED_AT);
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return array
     */
    public function getColumns(): array
    {
        if ($this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMNS)) {
            return explode(',', $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMNS));
        }
    }

    public function getProductImageWidth(): string
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_COLUMN_IMAGE_WIDTH);
    }
   
}
