<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating collection resource model
 *
 * @category   Mage
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Rating getItemById()
 */
class Mage_Rating_Model_Resource_Rating_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var bool
     */
    protected $_isStoreJoined = false;

    protected function _construct()
    {
        $this->_init('rating/rating');
    }

    /**
     * Add entity filter
     *
     * @param   int|string $entity
     * @return  Mage_Rating_Model_Resource_Rating_Collection
     */
    public function addEntityFilter($entity)
    {
        $adapter = $this->getConnection();

        $this->getSelect()
            ->join(
                $this->getTable('rating_entity'),
                'main_table.entity_id=' . $this->getTable('rating_entity') . '.entity_id',
                ['entity_code'],
            );

        if (is_numeric($entity)) {
            $this->addFilter(
                'entity',
                $adapter->quoteInto($this->getTable('rating_entity') . '.entity_id=?', $entity),
                'string',
            );
        } elseif (is_string($entity)) {
            $this->addFilter(
                'entity',
                $adapter->quoteInto($this->getTable('rating_entity') . '.entity_code=?', $entity),
                'string',
            );
        }
        return $this;
    }

    /**
     * set order by position field
     *
     * @param   string $dir
     * @return  Mage_Rating_Model_Resource_Rating_Collection
     */
    public function setPositionOrder($dir = 'ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int|array $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        $adapter = $this->getConnection();
        if (!is_array($storeId)) {
            $storeId = [$storeId ?? -1];
        }
        if (empty($storeId)) {
            return $this;
        }
        if (!$this->_isStoreJoined) {
            $this->getSelect()
                ->distinct(true)
                ->join(
                    ['store' => $this->getTable('rating_store')],
                    'main_table.rating_id = store.rating_id',
                    [],
                );
            $this->_isStoreJoined = true;
        }
        $inCond = $adapter->prepareSqlCondition('store.store_id', [
            'in' => $storeId,
        ]);
        $this->getSelect()
            ->where($inCond);
        $this->setPositionOrder();
        return $this;
    }

    /**
     * Add options to ratings in collection
     *
     * @return $this
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

    /**
     * Add entity summary to item
     *
     * @param int $entityPkValue
     * @param int $storeId
     * @return $this
     */
    public function addEntitySummaryToItem($entityPkValue, $storeId)
    {
        $arrRatingId = $this->getColumnValues('rating_id');
        if (count($arrRatingId) == 0) {
            return $this;
        }

        $adapter = $this->getConnection();

        $inCond = $adapter->prepareSqlCondition('rating_option_vote.rating_id', [
            'in' => $arrRatingId,
        ]);
        $sumCond = new Zend_Db_Expr("SUM(rating_option_vote.{$adapter->quoteIdentifier('percent')})");
        $countCond = new Zend_Db_Expr('COUNT(*)');
        $select = $adapter->select()
            ->from(
                ['rating_option_vote'  => $this->getTable('rating/rating_option_vote')],
                [
                    'rating_id' => 'rating_option_vote.rating_id',
                    'sum'         => $sumCond,
                    'count'       => $countCond,
                ],
            )
            ->join(
                ['review_store' => $this->getTable('review/review_store')],
                'rating_option_vote.review_id=review_store.review_id AND review_store.store_id = :store_id',
                [],
            )
            ->join(
                ['rst' => $this->getTable('rating/rating_store')],
                'rst.rating_id = rating_option_vote.rating_id AND rst.store_id = :rst_store_id',
                [],
            )
            ->join(
                ['review'              => $this->getTable('review/review')],
                'review_store.review_id=review.review_id AND review.status_id=1',
                [],
            )
            ->where($inCond)
            ->where('rating_option_vote.entity_pk_value=:pk_value')
            ->group('rating_option_vote.rating_id');
        $bind = [
            ':store_id' => (int) $storeId,
            ':rst_store_id' => (int) $storeId,
            ':pk_value'     => $entityPkValue,
        ];
        $data = $this->getConnection()->fetchAll($select, $bind);

        foreach ($data as $item) {
            $rating = $this->getItemById($item['rating_id']);
            if ($rating && $item['count'] > 0) {
                $rating->setSummary($item['sum'] / $item['count']);
            }
        }
        return $this;
    }

    /**
     * Add rating store name
     *
     * @param int $storeId
     * @return $this
     */
    public function addRatingPerStoreName($storeId)
    {
        $adapter = $this->getConnection();
        $ratingCodeCond = $adapter->getIfNullSql('title.value', 'main_table.rating_code');
        $this->getSelect()
            ->joinLeft(
                ['title' => $this->getTable('rating_title')],
                $adapter->quoteInto('main_table.rating_id=title.rating_id AND title.store_id = ?', (int) $storeId),
                ['rating_code' => $ratingCodeCond],
            );
        return $this;
    }

    /**
     * Add stores to collection
     *
     * @return $this
     */
    public function addStoresToCollection()
    {
        if (!$this->_isCollectionLoaded) {
            return $this;
        }
        $ratingIds = [];
        foreach ($this as $item) {
            $ratingIds[] = $item->getId();
            $item->setStores([]);
        }
        if (!$ratingIds) {
            return $this;
        }
        $adapter = $this->getConnection();

        $inCond = $adapter->prepareSqlCondition('rating_id', [
            'in' => $ratingIds,
        ]);

        $this->_select = $adapter
            ->select()
            ->from($this->getTable('rating_store'))
            ->where($inCond);

        $data = $adapter->fetchAll($this->_select);
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $item = $this->getItemById($row['rating_id']);
                $item->setStores(array_merge($item->getStores(), [$row['store_id']]));
            }
        }
        return $this;
    }
}
