<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Configurable product type resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init resource
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_super_link', 'link_id');
    }

    /**
     * Save configurable product relations
     *
     * @param Mage_Catalog_Model_Product|int $mainProduct the parent id
     * @param array $productIds the children id array
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable
     */
    public function saveProducts($mainProduct, $productIds)
    {
        $isProductInstance = false;
        if ($mainProduct instanceof Mage_Catalog_Model_Product) {
            $mainProductId = $mainProduct->getId();
            $isProductInstance = true;
        } else {
            $mainProductId = $mainProduct;
        }
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_id')
            ->where('parent_id=?', $mainProductId);
        $old    = $this->_getReadAdapter()->fetchCol($select);

        $insert = array_diff($productIds, $old);
        $delete = array_diff($old, $productIds);

        if ((!empty($insert) || !empty($delete)) && $isProductInstance) {
            $mainProduct->setIsRelationsChanged(true);
        }

        if (!empty($delete)) {
            $where = join(' AND ', array(
                $this->_getWriteAdapter()->quoteInto('parent_id=?', $mainProductId),
                $this->_getWriteAdapter()->quoteInto('product_id IN(?)', $delete)
            ));
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $childId) {
                $data[] = array(
                    'product_id' => $childId,
                    'parent_id'  => $mainProductId
                );
            }
            $this->_getWriteAdapter()->insertMultiple($this->getMainTable(), $data);
        }

        // configurable product relations should be added to relation table
        Mage::getResourceSingleton('catalog/product_relation')
            ->processRelations($mainProductId, $productIds);

        return $this;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        $childrenIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('l' => $this->getMainTable()), array('product_id', 'parent_id'))
            ->join(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id=l.product_id AND e.required_options=0',
                array()
            )
            ->where('parent_id=?', $parentId);

        $childrenIds = array(0 => array());
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $childrenIds[0][$row['product_id']] = $row['product_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        $parentIds = array();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('product_id', 'parent_id'))
            ->where('product_id IN(?)', $childId);
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $parentIds[] = $row['parent_id'];
        }

        return $parentIds;
    }
}
