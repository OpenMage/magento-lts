<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Type_Configurable extends Mage_Catalog_Model_Resource_Product_Type_Configurable
{
    /**
     * Retrieve Required children ids
     * Grouped by parent id.
     *
     * @param mixed $parentId may be array of integers or scalar integer.
     * @param bool $required
     * @return array
     * @see Mage_Catalog_Model_Resource_Product_Type_Configurable::getChildrenIds()
     */
    public function getChildrenIds($parentId, $required = true)
    {
        if (is_array($parentId)) {
            $childrenIds = [];
            if (!empty($parentId)) {
                $select = $this->_getReadAdapter()->select()
                    ->from(['l' => $this->getMainTable()], ['product_id', 'parent_id'])
                    ->join(
                        ['e' => $this->getTable('catalog/product')],
                        'e.entity_id = l.product_id AND e.required_options = 0',
                        [],
                    )
                    ->where('parent_id IN (?)', $parentId);

                foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
                    $childrenIds[$row['parent_id']][$row['product_id']] = $row['product_id'];
                }
            }
            return $childrenIds;
        } else {
            return parent::getChildrenIds($parentId, $required);
        }
    }
}
