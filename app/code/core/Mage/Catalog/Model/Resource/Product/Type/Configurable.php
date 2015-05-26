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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Configurable product type resource model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Product_Type_Configurable extends Mage_Core_Model_Resource_Db_Abstract
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
     * @return Mage_Catalog_Model_Resource_Product_Type_Configurable
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
        $old = $mainProduct->getTypeInstance()->getUsedProductIds();

        $insert = array_diff($productIds, $old);
        $delete = array_diff($old, $productIds);

        if ((!empty($insert) || !empty($delete)) && $isProductInstance) {
            $mainProduct->setIsRelationsChanged(true);
        }

        if (!empty($delete)) {
            $where = array(
                'parent_id = ?'     => $mainProductId,
                'product_id IN(?)'  => $delete
            );
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $childId) {
                $data[] = array(
                    'product_id' => (int)$childId,
                    'parent_id'  => (int)$mainProductId
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
                'e.entity_id = l.product_id AND e.required_options = 0',
                array()
            )
            ->where('parent_id = ?', $parentId);

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

    /**
     * Collect product options with values according to the product instance and attributes, that were received
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $attributes
     * @return array
     */
    public function getConfigurableOptions($product, $attributes)
    {
        $attributesOptionsData = array();
        foreach ($attributes as $superAttribute) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array(
                        'super_attribute'       => $this->getTable('catalog/product_super_attribute')
                    ),
                    array(
                        'sku'                   => 'entity.sku',
                        'product_id'            => 'super_attribute.product_id',
                        'attribute_code'        => 'attribute.attribute_code',
                        'option_title'          => 'option_value.value',
                        'pricing_value'         => 'attribute_pricing.pricing_value',
                        'pricing_is_percent'    => 'attribute_pricing.is_percent'
                    )
                )->joinInner(
                    array(
                        'product_link'          => $this->getTable('catalog/product_super_link')
                    ),
                    'product_link.parent_id = super_attribute.product_id',
                    array()
                )->joinInner(
                    array(
                        'attribute'             => $this->getTable('eav/attribute')
                    ),
                    'attribute.attribute_id = super_attribute.attribute_id',
                    array()
                )->joinInner(
                    array(
                        'entity'                => $this->getTable('catalog/product')
                    ),
                    'entity.entity_id = product_link.product_id',
                    array()
                )->joinInner(
                    array(
                        'entity_value'          => $superAttribute->getBackendTable()
                    ),
                    implode(
                        ' AND ',
                        array(
                            $this->_getReadAdapter()
                                ->quoteInto('entity_value.entity_type_id = ?', $product->getEntityTypeId()),
                            'entity_value.attribute_id = super_attribute.attribute_id',
                            'entity_value.store_id = 0',
                            'entity_value.entity_id = product_link.product_id'
                        )
                    ),
                    array()
                )->joinLeft(
                    array(
                        'option_value'          => $this->getTable('eav/attribute_option_value')
                    ),
                    implode(' AND ', array(
                        'option_value.option_id = entity_value.value',
                        'option_value.store_id = ' . Mage_Core_Model_App::ADMIN_STORE_ID,
                    )),
                    array()
                )->joinLeft(
                    array(
                        'attribute_pricing'     => $this->getTable('catalog/product_super_attribute_pricing')
                    ),
                    implode(' AND ', array(
                        'super_attribute.product_super_attribute_id = attribute_pricing.product_super_attribute_id',
                        'entity_value.value = attribute_pricing.value_index'
                    )),
                    array()
                )->where('super_attribute.product_id = ?', $product->getId());

            $attributesOptionsData[$superAttribute->getAttributeId()] = $this->_getReadAdapter()->fetchAll($select);
        }
        return $attributesOptionsData;
    }
}
