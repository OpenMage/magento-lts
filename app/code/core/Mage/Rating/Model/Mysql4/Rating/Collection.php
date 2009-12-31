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
 * @package     Mage_Rating
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating collection resource model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rating_Model_Mysql4_Rating_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @var bool
     */
    protected $_isStoreJoined = false;

    public function _construct()
    {
        $this->_init('rating/rating');
    }



    /**
     * add entity filter
     *
     * @param   int|string $entity
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity)
    {
        $this->_select->join($this->getTable('rating_entity'),
           'main_table.entity_id='.$this->getTable('rating_entity').'.entity_id');

        if (is_numeric($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->getTable('rating_entity').'.entity_id=?', $entity),
                'string');
        }
        elseif (is_string($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->getTable('rating_entity').'.entity_code=?', $entity),
                'string');
        }
        return $this;
    }

    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Varien_Data_Collection_Db
     */
    public function setPositionOrder($dir='ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }

    public function setStoreFilter($storeId)
    {
        if(!is_array($storeId)) {
           $storeId = array($storeId === null ? -1 : $storeId);
        }
        if (empty($storeId)) {
            return $this;
        }
        if (!$this->_isStoreJoined) {
            $this->getSelect()->join(array('store'=>$this->getTable('rating_store')), 'main_table.rating_id = store.rating_id')
                ->group('main_table.rating_id');
            $this->_isStoreJoined = true;
        }
        $this->getSelect()->where($this->getConnection()->quoteInto('store.store_id IN(?)', $storeId));
        $this->setPositionOrder();
        return $this;
    }

    /**
     * add options to ratings in collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function addOptionToItems()
    {
        $arrRatingId = $this->getColumnValues('rating_id');

        if (!empty($arrRatingId)) {
            $collection = Mage::getResourceModel('rating/rating_option_collection')
                ->addRatingFilter($arrRatingId)
                ->setPositionOrder()
                ->load();

            foreach ($this as $rating) {
                $rating->setOptions($collection->getItemsByColumnValue('rating_id', $rating->getId()));
            }
        }

        return $this;
    }

    public function addEntitySummaryToItem($entityPkValue, $storeId)
    {
        $arrRatingId = $this->getColumnValues('rating_id');

        if( count($arrRatingId) == 0 ) {
            return;
        }

        $sql = "SELECT
                    {$this->getTable('rating_vote')}.rating_id as rating_id,
                    SUM({$this->getTable('rating_vote')}.percent) as sum,
                    COUNT(*) as count
                FROM
                    {$this->getTable('rating_vote')}
                INNER JOIN
                    {$this->getTable('review/review_store')}
                    ON {$this->getTable('rating_vote')}.review_id={$this->getTable('review/review_store')}.review_id AND {$this->getTable('review/review_store')}.store_id = ". (int) $storeId . "
                INNER JOIN
                    {$this->getTable('rating/rating_store')} AS rst
                    ON rst.rating_id = {$this->getTable('rating_vote')}.rating_id AND rst.store_id = ". (int) $storeId . "
                INNER JOIN
                    {$this->getTable('review/review')} ON {$this->getTable('review/review_store')}.review_id={$this->getTable('review/review')}.review_id AND {$this->getTable('review/review')}.status_id=1
                WHERE
                    {$this->getConnection()->quoteInto($this->getTable('rating_vote').'.rating_id IN (?)', $arrRatingId)}
                    AND {$this->getConnection()->quoteInto($this->getTable('rating_vote').'.entity_pk_value=?', $entityPkValue)}
                GROUP BY
                    {$this->getTable('rating_vote')}.rating_id";

        $data = $this->getConnection()->fetchAll($sql);

        foreach ($data as $item) {
            $rating = $this->getItemById($item['rating_id']);
                if ($rating && $item['count']>0) {
                    $rating->setSummary($item['sum']/$item['count']);
                }
        }
        return $this;
    }

    public function addRatingPerStoreName($storeId) {
        $this->getSelect()->joinLeft(array('title'=>$this->getTable('rating_title')),
                          'main_table.rating_id=title.rating_id AND title.store_id = '. (int) $storeId,
                          array('IF(title.value IS NULL, main_table.rating_code, title.value) AS rating_code'));
        return $this;
    }

    public function addStoresToCollection()
    {
        if (!$this->_isCollectionLoaded) {
            return $this;
        }
        $ratingIds = array();
        foreach ($this as $item) {
            $ratingIds[] = $item->getId();
            $item->setStores(array());
        }
        if (!$ratingIds) {
            return $this;
        }

        $this->_select = $this->getConnection()
            ->select()
            ->from($this->getTable('rating_store'))
            ->where('rating_id IN(?)', $ratingIds);
        foreach ($this->getConnection()->fetchAll($this->_select) as $row) {
            $item = $this->getItemById($row['rating_id']);
            $item->setStores(array_merge($item->getStores(), array($row['store_id'])));
        }

        return $this;
    }
}
