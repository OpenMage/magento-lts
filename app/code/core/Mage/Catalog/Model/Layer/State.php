<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Layered navigation state model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Layer_State extends Varien_Object
{
    /**
     * Add filter item to layer state
     *
     * @param   Mage_Catalog_Model_Layer_Filter_Item $filter
     * @return  Mage_Catalog_Model_Layer_State
     */
    public function addFilter($filter)
    {
        $filters = $this->getFilters();
        $filters[] = $filter;
        $this->setFilters($filters);
        return $this;
    }

    /**
     * Set layer state filter items
     *
     * @param   array $filters
     * @return  Mage_Catalog_Model_Layer_State
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            Mage::throwException(Mage::helper('catalog')->__('The filters must be an array.'));
        }
        $this->setData('filters', $filters);
        return $this;
    }

    /**
     * Get applied to layer filter items
     *
     * @return array
     */
    public function getFilters()
    {
        $filters = $this->getData('filters');
        if (is_null($filters)) {
            $filters = [];
            $this->setData('filters', $filters);
        }
        return $filters;
    }
}
