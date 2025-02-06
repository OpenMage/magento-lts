<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog category EAV additional attribute resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * Main select object initialization.
     * Joins catalog/eav_attribute table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getResource()->getMainTable()])
            ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Category::ENTITY)->getTypeId())
            ->join(
                ['additional_table' => $this->getTable('catalog/eav_attribute')],
                'additional_table.attribute_id = main_table.attribute_id',
            );
        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param int $typeId
     * @return $this
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
