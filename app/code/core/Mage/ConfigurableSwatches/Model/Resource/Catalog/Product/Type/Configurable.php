<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @author     Magento Core Team <core@magentocommerce.com>
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
                        []
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
