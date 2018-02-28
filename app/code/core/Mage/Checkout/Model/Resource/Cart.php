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
 * @package     Mage_Checkout
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Resource model for Checkout Cart
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Model_Resource_Cart extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales/quote', 'entity_id');
    }

    /**
     * Fetch items summary
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItemsSummary($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('q'=>$this->getTable('sales/quote')), array('items_qty', 'items_count'))
            ->where('q.entity_id = :quote_id');

        $result = $read->fetchRow($select, array(':quote_id' => $quoteId));
        return $result ? $result : array('items_qty'=>0, 'items_count'=>0);
    }

    /**
     * Fetch items
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItems($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('qi'=>$this->getTable('sales/quote_item')),
                array('id'=>'item_id', 'product_id', 'super_product_id', 'qty', 'created_at'))
            ->where('qi.quote_id = :quote_id');

        return $read->fetchAll($select, array(':quote_id' => $quoteId));
    }

    /**
     * Make collection not to load products that are in specified quote
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @param int $quoteId
     * @return Mage_Checkout_Model_Resource_Cart
     */
    public function addExcludeProductFilter($collection, $quoteId)
    {
        $adapter = $this->_getReadAdapter();
        $exclusionSelect = $adapter->select()
            ->from($this->getTable('sales/quote_item'), array('product_id'))
            ->where('quote_id = ?', $quoteId);
        $condition = $adapter->prepareSqlCondition('e.entity_id', array('nin' => $exclusionSelect));
        $collection->getSelect()->where($condition);
        return $this;
    }
}
