<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Grouped_Collection extends Varien_Data_Collection //Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Column name for group by clause
     *
     * @var string
     */
    protected $_columnGroupBy       = null;

    /**
     * Collection resource
     *
     * @var Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_resourceCollection  = null;

    /**
     * Set column to group by
     *
     * @param string $column
     * @return $this
     */
    public function setColumnGroupBy($column)
    {
        $this->_columnGroupBy = (string) $column;
        return $this;
    }

    /**
     * Load collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        parent::load($printQuery, $logQuery);
        $this->_setIsLoaded();

        if ($this->_columnGroupBy !== null) {
            $this->_mergeWithEmptyData();
            $this->_groupResourceData();
        }

        return $this;
    }

    /**
     * Setter for resource collection
     *
     * @param Varien_Data_Collection_Db $collection
     * @return $this
     */
    public function setResourceCollection($collection)
    {
        $this->_resourceCollection = $collection;
        return $this;
    }

    /**
     * Merge empty data collection with resource collection
     *
     * @return $this
     */
    protected function _mergeWithEmptyData()
    {
        if (count($this->_items) == 0) {
            return $this;
        }

        foreach ($this->_items as $key => $item) {
            foreach ($this->_resourceCollection as $dataItem) {
                if ($item->getData($this->_columnGroupBy) == $dataItem->getData($this->_columnGroupBy)) {
                    if ($this->_items[$key]->getIsEmpty()) {
                        $this->_items[$key] = $dataItem;
                    } else {
                        $this->_items[$key]->addChild($dataItem);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Group data in resource collection
     *
     * @return $this
     */
    protected function _groupResourceData()
    {
        if (count($this->_items) == 0) {
            foreach ($this->_resourceCollection as $item) {
                if (isset($this->_items[$item->getData($this->_columnGroupBy)])) {
                    $this->_items[$item->getData($this->_columnGroupBy)]->addChild($item->setIsEmpty(false));
                } else {
                    $this->_items[$item->getData($this->_columnGroupBy)] = $item->setIsEmpty(false);
                }
            }
        }

        return $this;
    }
}
