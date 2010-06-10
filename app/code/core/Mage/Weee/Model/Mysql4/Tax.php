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
 * @package     Mage_Weee
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Weee_Model_Mysql4_Tax extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('weee/tax', 'value_id');
    }

    public function fetchOne($select)
    {
        return $this->_getReadAdapter()->fetchOne($select);
    }

    public function fetchCol($select)
    {
        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function updateDiscountPercents()
    {
        return $this->_updateDiscountPercents();
    }

    public function updateProductsDiscountPercent($condition)
    {
        return $this->_updateDiscountPercents($condition);
    }

    /**
     * Update tax percents for WEEE based on products condition
     *
     * @param   mixed $productCondition
     * @return  Mage_Weee_Model_Mysql4_Tax
     */
    protected function _updateDiscountPercents($productCondition = null)
    {
        $now = strtotime(now());

        $select = $this->_getReadAdapter()->select();
        $select->from(array('data'=>$this->getTable('catalogrule/rule_product')));

        $deleteCondition = '';
        if ($productCondition) {
            if ($productCondition instanceof Mage_Catalog_Model_Product) {
                $select->where('product_id=?', $productCondition->getId());
                $deleteCondition = $this->_getWriteAdapter()->quoteInto('entity_id=?', $productCondition->getId());
            } elseif ($productCondition instanceof Mage_Catalog_Model_Product_Condition_Interface) {
                $productCondition = $productCondition->getIdsSelect($this->_getWriteAdapter())->__toString();
                $select->where('product_id IN ('.$productCondition.')');
                $deleteCondition = 'entity_id IN ('.$productCondition.')';
            } else {
                $select->where('product_id=?', $productCondition);
                $deleteCondition = $this->_getWriteAdapter()->quoteInto('entity_id=?', $productCondition);
            }
        } else {
            $select->where('(from_time <= ? OR from_time = 0)', $now)
                ->where('(to_time >= ? OR to_time = 0)', $now);
        }
        $this->_getWriteAdapter()->delete($this->getTable('weee/discount'), $deleteCondition);

        $select->order(array('data.website_id', 'data.customer_group_id', 'data.product_id', 'data.sort_order'));

        $data = $this->_getReadAdapter()->query($select);

        $productData = array();
        $stops = array();
        $prevKey = false;
        while ($row = $data->fetch()) {
            $key = "{$row['product_id']}-{$row['website_id']}-{$row['customer_group_id']}";
            if (isset($stops[$key]) && $stops[$key]) {
                continue;
            }

            if ($prevKey && ($prevKey != $key)) {
                foreach ($productData as $product) {
                    $this->_getWriteAdapter()->insert($this->getTable('weee/discount'), $product);
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
            $this->_getWriteAdapter()->insert($this->getTable('weee/discount'), $product);
        }
        return $this;
    }

    public function getProductDiscountPercent($product, $website, $group)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getTable('weee/discount'), 'value')
            ->where('website_id = ?', $website)
            ->where('entity_id = ?', $product)
            ->where('customer_group_id = ?', $group);

        return $this->_getReadAdapter()->fetchOne($select);
    }
}
