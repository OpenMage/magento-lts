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
 * @package     Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Wee tax resource model
 *
 * @category    Mage
 * @package     Mage_Weee
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Weee_Model_Resource_Tax extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }

    /**
     * Fetch one
     *
     * @param Varien_Db_Select|string $select
     * @return string
     */
    public function fetchOne($select)
    {
        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Fetch column
     *
     * @param Varien_Db_Select|string $select
     * @return array
     */
    public function fetchCol($select)
    {
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Update discount percents
     *
     * @return $this
     */
    public function updateDiscountPercents()
    {
        return $this->_updateDiscountPercents();
    }

    /**
     * Update products discount persent
     *
     * @param mixed $condition
     * @return $this
     */
    public function updateProductsDiscountPercent($condition)
    {
        return $this->_updateDiscountPercents($condition);
    }

    /**
     * Update tax percents for WEEE based on products condition
     *
     * @param mixed $productCondition
     * @return $this
     */
    protected function _updateDiscountPercents($productCondition = null)
    {
        $now     = Varien_Date::toTimestamp(Varien_Date::now());
        $adapter = $this->_getWriteAdapter();

        $select  = $this->_getReadAdapter()->select();
        $select->from(array('data' => $this->getTable('catalogrule/rule_product')));

        $deleteCondition = '';
        if ($productCondition) {
            if ($productCondition instanceof Mage_Catalog_Model_Product) {
                $select->where('product_id = ?', (int)$productCondition->getId());
                $deleteCondition = $adapter->quoteInto('entity_id=?', (int)$productCondition->getId());
            } elseif ($productCondition instanceof Mage_Catalog_Model_Product_Condition_Interface) {
                $productCondition = $productCondition->getIdsSelect($adapter)->__toString();
                $select->where("product_id IN ({$productCondition})");
                $deleteCondition = "entity_id IN ({$productCondition})";
            } else {
                $select->where('product_id = ?', (int)$productCondition);
                $deleteCondition = $adapter->quoteInto('entity_id = ?', (int)$productCondition);
            }
        } else {
            $select->where('(from_time <= ? OR from_time = 0)', $now)
                   ->where('(to_time >= ? OR to_time = 0)', $now);
        }
        $adapter->delete($this->getTable('weee/discount'), $deleteCondition);

        $select->order(array('data.website_id', 'data.customer_group_id', 'data.product_id', 'data.sort_order'));

        $data = $this->_getReadAdapter()->query($select);

        $productData = array();
        $stops       = array();
        $prevKey     = false;
        while ($row = $data->fetch()) {
            $key = "{$row['product_id']}-{$row['website_id']}-{$row['customer_group_id']}";
            if (isset($stops[$key]) && $stops[$key]) {
                continue;
            }

            if ($prevKey && ($prevKey != $key)) {
                foreach ($productData as $product) {
                    $adapter->insert($this->getTable('weee/discount'), $product);
                }
                $productData = array();
            }
            if ($row['action_operator'] == 'by_percent') {
                if (isset($productData[$key])) {
                    $productData[$key]['value'] -= $productData[$key]['value']/100*$row['action_amount'];
                } else {
                    $productData[$key] = array(
                        'entity_id'         => $row['product_id'],
                        'customer_group_id' => $row['customer_group_id'],
                        'website_id'        => $row['website_id'],
                        'value'             => 100-max(0, min(100, $row['action_amount'])),
                    );
                }
            }

            if ($row['action_stop']) {
                $stops[$key] = true;
            }
            $prevKey = $key;
        }
        foreach ($productData as $product) {
            $adapter->insert($this->getTable('weee/discount'), $product);
        }

        return $this;
    }

    /**
     * Retrieve product discount percent
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $customerGroupId
     * @return string
     */
    public function getProductDiscountPercent($productId, $websiteId, $customerGroupId)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('weee/discount'), 'value')
            ->where('website_id = ?', (int)$websiteId)
            ->where('entity_id = ?', (int)$productId)
            ->where('customer_group_id = ?', (int)$customerGroupId);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}

