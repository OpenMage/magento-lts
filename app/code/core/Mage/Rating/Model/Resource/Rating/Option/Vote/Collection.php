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
 * Rating votes collection
 *
 * @category   Mage
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Rating_Option_Vote[] getItems()
 */
class Mage_Rating_Model_Resource_Rating_Option_Vote_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define model
     *
     */
    protected function _construct()
    {
        $this->_init('rating/rating_option_vote');
    }

    /**
     * Set review filter
     *
     * @param int $reviewId
     * @return $this
     */
    public function setReviewFilter($reviewId)
    {
        $this->getSelect()
            ->where('main_table.review_id = ?', $reviewId);
        return $this;
    }

    /**
     * Set EntityPk filter
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityPkFilter($entityId)
    {
        $this->getSelect()
            ->where('entity_pk_value = ?', $entityId);
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        $this->getSelect()
            ->join(
                ['rstore' => $this->getTable('review/review_store')],
                $this->getConnection()->quoteInto(
                    'main_table.review_id=rstore.review_id AND rstore.store_id=?',
                    (int) $storeId,
                ),
                [],
            );
        return $this;
    }

    /**
     * Add rating info to select
     *
     * @param int $storeId
     * @return $this
     */
    public function addRatingInfo($storeId = null)
    {
        $adapter = $this->getConnection();
        $ratingCodeCond = $adapter->getIfNullSql('title.value', 'rating.rating_code');
        $this->getSelect()
            ->join(
                ['rating'    => $this->getTable('rating/rating')],
                'rating.rating_id = main_table.rating_id',
                ['rating_code'],
            )
            ->joinLeft(
                ['title' => $this->getTable('rating/rating_title')],
                $adapter->quoteInto(
                    'main_table.rating_id=title.rating_id AND title.store_id = ?',
                    (int) Mage::app()->getStore()->getId(),
                ),
                ['rating_code' => $ratingCodeCond],
            );

        if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (is_array($storeId)) {
            $condition = $adapter->prepareSqlCondition('store.store_id', [
                'in' => $storeId,
            ]);
        } else {
            $condition = $adapter->quoteInto('store.store_id = ?', $storeId);
        }

        $this->getSelect()
            ->join(
                ['store' => $this->getTable('rating_store')],
                'main_table.rating_id = store.rating_id AND ' . $condition,
            );

        return $this;
    }

    /**
     * Add option info to select
     *
     * @return $this
     */
    public function addOptionInfo()
    {
        $this->getSelect()
            ->join(
                ['rating_option' => $this->getTable('rating/rating_option')],
                'main_table.option_id = rating_option.option_id',
            );
        return $this;
    }

    /**
     * Add rating options
     *
     * @return $this
     */
    public function addRatingOptions()
    {
        if (!$this->getSize()) {
            return $this;
        }
        foreach ($this->getItems() as $item) {
            $options = Mage::getModel('rating/rating_option')
                    ->getResourceCollection()
                    ->addRatingFilter($item->getRatingId())
                    ->load();

            if ($item->getRatingId()) {
                $item->setRatingOptions($options);
            } else {
                return $this;
            }
        }
        return $this;
    }
}
