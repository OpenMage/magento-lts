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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Product  Samples resource model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Mysql4_Sample extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection
     *
     */
    protected function  _construct()
    {
        $this->_init('downloadable/sample', 'sample_id');
    }

    /**
     * Save title of sample item in store scope
     *
     * @param Mage_Downloadable_Model_Sample $sampleObject
     * @return Mage_Downloadable_Model_Mysql4_Sample
     */
    public function saveItemTitle($sampleObject)
    {
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('downloadable/sample_title'))
            ->where('sample_id = ?', $sampleObject->getId())
            ->where('store_id = ?', $sampleObject->getStoreId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $sampleObject->getId()) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $sampleObject->getStoreId());
            if ($sampleObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->delete(
                    $this->getTable('downloadable/sample_title'), $where);
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getTable('downloadable/sample_title'),
                    array('title' => $sampleObject->getTitle()), $where);
            }
        } else {
            if (!$sampleObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('downloadable/sample_title'),
                    array(
                        'sample_id' => $sampleObject->getId(),
                        'store_id' => $sampleObject->getStoreId(),
                        'title' => $sampleObject->getTitle(),
                    ));
            }
        }
        return $this;
    }

    /**
     * Delete data by item(s)
     *
     * @param Mage_Downloadable_Model_Sample|array|int $items
     * @return Mage_Downloadable_Model_Mysql4_Sample
     */
    public function deleteItems($items)
    {
        $where = '';
        if ($items instanceof Mage_Downloadable_Model_Sample) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $items->getId());
        }
        elseif (is_array($items)) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id in (?)', $items);
        }
        else {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $items);
        }
        if ($where) {
            $this->_getReadAdapter()->delete(
                $this->getTable('downloadable/sample'),$where);
            $this->_getReadAdapter()->delete(
                $this->getTable('downloadable/sample_title'), $where);
        }
        return $this;
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('sample' => $this->getMainTable()), null)
            ->join(
                array('sample_title_default' => $this->getTable('downloadable/sample_title')),
                'sample_title_default.sample_id=sample.sample_id AND sample_title_default.store_id=0',
                array())
            ->joinLeft(
                array('sample_title_store' => $this->getTable('downloadable/sample_title')),
                'sample_title_store.sample_id=sample.sample_id AND sample_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(sample_title_store.title, sample_title_default.title)'))
            ->where('sample.product_id=?', $productId);
        if (!$searchData = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array();
        }
        return $searchData;
    }
}
