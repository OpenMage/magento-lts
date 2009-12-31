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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog product link resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_attributesTable;
    protected function  _construct()
    {
        $this->_init('catalog/product_link', 'link_id');
        $this->_attributesTable = $this->getTable('catalog/product_link_attribute');
    }

    public function saveProductLinks($product, $data, $typeId)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $attributes = $this->getAttributesByType($typeId);
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId())
            . $this->_getWriteAdapter()->quoteInto(' AND link_type_id=?', $typeId);

        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $linkedProductId => $linkInfo) {
            $this->_getWriteAdapter()->insert($this->getMainTable(), array(
                'product_id'        => $product->getId(),
                'linked_product_id' => $linkedProductId,
                'link_type_id'      => $typeId
            ));
            $linkId = $this->_getWriteAdapter()->lastInsertId();
            foreach ($attributes as $attributeInfo) {
                $attributeTable = $this->getAttributeTypeTable($attributeInfo['type']);
            	if ($attributeTable && isset($linkInfo[$attributeInfo['code']])) {
                    $this->_getWriteAdapter()->insert($attributeTable, array(
                        'product_link_attribute_id' => $attributeInfo['id'],
                        'link_id'                   => $linkId,
                        'value'                     => $linkInfo[$attributeInfo['code']]
                    ));
            	}
            }
        }
        return $this;
    }

    public function getAttributesByType($typeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->_attributesTable, array(
                'id'    => 'product_link_attribute_id',
                'code'  => 'product_link_attribute_code',
                'type'  => 'data_type'
            ))
            ->where('link_type_id=?', $typeId);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    public function getAttributeTypeTable($type)
    {
        return $this->getTable('catalog/product_link_attribute_'.$type);
    }

/**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param int $typeId
     * @return array
     */
    public function getChildrenIds($parentId, $typeId)
    {
        $childrenIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('l' => $this->getMainTable()), array('product_id', 'linked_product_id'))
            ->where('product_id=?', $parentId)
            ->where('link_type_id=?', $typeId);
        if ($typeId == Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED) {
            $select->join(
                array('e' => $this->getTable('catalog/product')),
                'e.entity_id=l.linked_product_id AND e.required_options=0',
                array()
            );
        }

        $childrenIds[$typeId] = array();
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $childrenIds[$typeId][$row['linked_product_id']] = $row['linked_product_id'];
        }

        return $childrenIds;
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @param int $typeId
     * @return array
     */
    public function getParentIdsByChild($childId, $typeId)
    {
        $parentIds = array();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array('product_id', 'linked_product_id'))
            ->where('linked_product_id IN(?)', $childId)
            ->where('link_type_id=?', $typeId);
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $parentIds[] = $row['product_id'];
        }

        return $parentIds;
    }
}