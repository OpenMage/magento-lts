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
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Catalog Grid Config Advanced Helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Helper_Widget_Grid_Config_Abstract extends Mage_Core_Helper_Abstract implements Mage_Adminhtml_Helper_Widget_Grid_Config_Interface
{
    public const CONFIG_PATH_GRID_ENABLED = 'advanced_grid/%s/enabled';
    public const CONFIG_PATH_GRID_ORDER = 'advanced_grid/%s/order';
    public const CONFIG_PATH_ENABLE_REARRANGE_COLUMNS = 'advanced_grid/general/enabled_rearrange_columns';
    public const ACL_RESOURCE_REARRANGE_COLUMNS = 'admin/system/rearrange_grids_columns';

    /**
     * Scope grid id for configurations
     *
     * @var string
     */
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
     * @return string
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
    {
        if (!$this->_gridId) {
            Mage::throwException(Mage::helper('adminhtml')->__('Grid Id must be set.'));
        }
        $config = sprintf($configPath, $this->_gridId);
        return Mage::getStoreConfig($config);
    }


    public function saveOrderColumns($value)
    {
        $configPath = sprintf(self::CONFIG_PATH_GRID_ORDER, $this->_gridId);
        Mage::getModel('core/config')->saveConfig($configPath, $value);
    }

    public function getOrderColumns(): array
    {
        $data = $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_ORDER);
        if (!$data) {
            return [];
        }

        $data = Mage::helper('core')->jsonDecode($data);
        return $data;
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->getStoreConfigGridId(self::CONFIG_PATH_GRID_ENABLED) ?: false;
    }

    /**
     * Get grid enabled for custom columns
     *
     * @return bool
     */
    public function isRearrangeEnabled(): bool
    {
        return
            Mage::getStoreConfigFlag(self::CONFIG_PATH_ENABLE_REARRANGE_COLUMNS) &&
            Mage::getSingleton('admin/session')->isAllowed(self::ACL_RESOURCE_REARRANGE_COLUMNS);
    }

    /**
     * Collection object
     * @param Varien_Data_Collection_Db $collection
     *
     * return $this
     */
    abstract public function applyAdvancedGridCollection($collection);

    /**
     * Adminhtml grid widget block
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     *
     * return $this
     */
    abstract public function applyAdvancedGridColumn($gridBlock);
}
