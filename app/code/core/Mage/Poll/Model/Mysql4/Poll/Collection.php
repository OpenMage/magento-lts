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
 * @category   Mage
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Pool Mysql4 collection model resource
 *
 * @category   Mage
 * @package    Mage_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Poll_Model_Mysql4_Poll_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('poll/poll');
    }

    /**
     * Redefine default filters
     *
     * @param string $field
     * @param mixed $condition
     * @return Varien_Data_Collection_Db
     */
    public function addFieldToFilter($field, $condition=null)
    {
        if ($field == 'stores') {
            return $this->addStoresFilter($condition);
        }
        else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Add Stores Filter
     *
     * @param int $storeId
     * @return Mage_Poll_Model_Mysql4_Poll_Collection
     */
    public function addStoresFilter($storeId)
    {
        $this->_select->join(
            array('store' => $this->getTable('poll/poll_store')),
            'main_table.poll_id=store.poll_id AND store.store_id=' . (int)$storeId,
            array()
        );
        return $this;
    }

    /**
     * Add stores data
     *
     * @return Mage_Poll_Model_Mysql4_Poll_Collection
     */
    public function addStoreData()
    {
        $pollIds = $this->getColumnValues('poll_id');
        $storesToPoll = array();

        if (count($pollIds) > 0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('poll/poll_store'))
                ->where('poll_id IN(?)', $pollIds);
            $result = $this->getConnection()->fetchAll($select);

            foreach ($result as $row) {
                if (!isset($storesToPoll[$row['poll_id']])) {
                    $storesToPoll[$row['poll_id']] = array();
                }
                $storesToPoll[$row['poll_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if(isset($storesToPoll[$item->getId()])) {
                $item->setStores($storesToPoll[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    public function addSelectStores()
    {
        $pollId = $this->getId();
        $select = $this->getConnection()->select()
            ->from($this->getTable('poll/poll_store'))
            ->where('poll_id = ?', $pollId);
        $result = $this->getConnection()->fetchAll($select);
        $stores = array();
        foreach ($result as $row) {
            $stores[] = $row['stor_id'];
        }
        $this->setSelectStores($stores);

        return $this;
    }
}