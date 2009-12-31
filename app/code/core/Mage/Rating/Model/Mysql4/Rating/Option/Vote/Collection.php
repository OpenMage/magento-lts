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
 * @category   Mage
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rating votes collection
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rating_Model_Mysql4_Rating_Option_Vote_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('rating/rating_option_vote');
    }

    public function setReviewFilter($reviewId)
    {
        $this->_select->where("main_table.review_id = ?", $reviewId);
        return $this;
    }

    public function setEntityPkFilter($entityId)
    {
        $this->_select->where("entity_pk_value = ?", $entityId);
        return $this;
    }

    public function setStoreFilter($storeId)
    {
        $this->_select->join(array('rstore'=>$this->getTable('review/review_store')), 'main_table.review_id=rstore.review_id AND rstore.store_id=' . (int)$storeId, array());
        return $this;
    }

    public function addRatingInfo($storeId=null)
    {
        $this->_select->join($this->getTable('rating/rating'), "{$this->getTable('rating/rating')}.rating_id = main_table.rating_id", "{$this->getTable('rating/rating')}.*")
            ->joinLeft(array('title'=>$this->getTable('rating/rating_title')),
                          "main_table.rating_id=title.rating_id AND title.store_id = ". (int) Mage::app()->getStore()->getId(),
                          array("IF(title.value IS NULL, {$this->getTable('rating/rating')}.rating_code, title.value) AS rating_code"));

        if($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if(is_array($storeId)) {
            $condition = $this->getConnection()->quoteInto('store.store_id IN(?)', $storeId);
        } else {
            $condition = $this->getConnection()->quoteInto('store.store_id = ?', $storeId);
        }

        $this->_select->join(array('store'=>$this->getTable('rating_store')), 'main_table.rating_id = store.rating_id AND '. $condition);
        $this->_select->group('main_table.vote_id');

        return $this;
    }

    public function addOptionInfo()
    {
        $this->_select->join($this->getTable('rating/rating_option'), "main_table.option_id = {$this->getTable('rating/rating_option')}.option_id", "{$this->getTable('rating/rating_option')}.*");
        return $this;
    }

    public function addRatingOptions()
    {
        if( !$this->getSize() ) {
            return $this;
        }
        foreach( $this->getItems() as $item ) {
            $options = Mage::getModel('rating/rating_option')
                    ->getResourceCollection()
                    ->addRatingFilter($item->getRatingId())
                    ->load();

            if( $item->getRatingId() ) {
                $item->setRatingOptions($options);
            } else {
                return;
            }
        }
        return $this;
    }
}