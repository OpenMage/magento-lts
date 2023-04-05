<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ConfigurableSwatches_Model_Resource_Catalog_Product_Attribute_Super_Collection extends Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection
{
    private $_eavAttributesJoined = false;
    private $_storeId = null;

    /**
     * Filter parent products to these IDs
     *
     * @param array $parentProductIds
     * @return $this
     */
    public function addParentProductsFilter(array $parentProductIds)
    {
        $this->addFieldToFilter('product_id', ['in' => $parentProductIds]);
        return $this;
    }

    /**
     * Attach (join) info from eav_attribute table
     *
     * @return $this
     */
    public function attachEavAttributes()
    {
        if ($this->_eavAttributesJoined) {
            return $this;
        }

        $this->join(
            ['eav_attributes' => 'eav/attribute'],
            '`eav_attributes`.`attribute_id` = `main_table`.`attribute_id`'
        );

        $this->_eavAttributesJoined = true;
        return $this;
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->_storeId;
    }

    /**
     * Bypass parent _afterLoad() -- parent depends on single product context
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Resource_Db_Collection_Abstract::_afterLoad();
        $this->_loadOptionLabels();
        return $this;
    }

    /**
     * Load attribute option labels for current store and default (fallback)
     *
     * @return $this
     */
    protected function _loadOptionLabels()
    {
        $labels = $this->_getOptionLabels();
        foreach ($this->getItems() as $item) {
            $item->setOptionLabels($labels);
        }
        return $this;
    }

    /**
     * Get Option Labels
     *
     * @return array
     */
    protected function _getOptionLabels()
    {
        $attributeIds = $this->_getAttributeIds();

        $select = $this->getConnection()->select();
        $select->from(['options' => $this->getTable('eav/attribute_option')])
            ->join(
                ['labels' => $this->getTable('eav/attribute_option_value')],
                'labels.option_id = options.option_id',
                [
                    'label' => 'labels.value',
                    'store_id' => 'labels.store_id',
                ]
            )
            ->where('options.attribute_id IN (?)', $attributeIds)
            ->where(
                'labels.store_id IN (?)',
                [Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID, $this->getStoreId()]
            );

        $resultSet = $this->getConnection()->query($select);
        $labels = [];
        while ($option = $resultSet->fetch()) {
            $labels[$option['option_id']][$option['store_id']] = $option['label'];
        }
        return $labels;
    }

    /**
     * Get Attribute IDs
     *
     * @return array
     */
    protected function _getAttributeIds()
    {
        $attributeIds = [];
        foreach ($this->getItems() as $item) {
            $attributeIds[] = $item->getAttributeId();
        }
        $attributeIds = array_unique($attributeIds);

        return $attributeIds;
    }
}
