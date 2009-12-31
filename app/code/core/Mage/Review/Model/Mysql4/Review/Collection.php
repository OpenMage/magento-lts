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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review collection resource model
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Mysql4_Review_Collection extends Varien_Data_Collection_Db
{
    protected $_reviewTable;
    protected $_reviewDetailTable;
    protected $_reviewStatusTable;
    protected $_reviewEntityTable;
    protected $_reviewStoreTable;
    protected $_addStoreDataFlag = false;

    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('review_read'));

        $this->_reviewTable         = $resources->getTableName('review/review');
        $this->_reviewDetailTable   = $resources->getTableName('review/review_detail');
        $this->_reviewStatusTable   = $resources->getTableName('review/review_status');
        $this->_reviewEntityTable   = $resources->getTableName('review/review_entity');
        $this->_reviewStoreTable   = $resources->getTableName('review/review_store');

        $this->_select->from(array('main_table'=>$this->_reviewTable))
            ->join(array('detail'=>$this->_reviewDetailTable), 'main_table.review_id=detail.review_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('review/review'));
    }

    public function addCustomerFilter($customerId)
    {
        $this->addFilter('customer',
            $this->getConnection()->quoteInto('detail.customer_id=?', $customerId),
            'string');
        return $this;
    }

    /**
     * Add store filter
     *
     * @param   int|array $storeId
     * @return  Varien_Data_Collection_Db
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()->join(array('store'=>$this->_reviewStoreTable), 'main_table.review_id=store.review_id', array());
        $this->getSelect()->where('store.store_id IN (?)', $storeId);
        return $this;
    }

    /**
     * Add stores data
     *
     * @param   int $storeId
     * @return  Varien_Data_Collection_Db
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param   int|string $entity
     * @param   int $pkValue
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity, $pkValue)
    {
        if (is_numeric($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto('main_table.entity_id=?', $entity),
                'string');
        }
        elseif (is_string($entity)) {
            $this->_select->join($this->_reviewEntityTable,
                'main_table.entity_id='.$this->_reviewEntityTable.'.entity_id');

            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_reviewEntityTable.'.entity_code=?', $entity),
                'string');
        }

        $this->addFilter('entity_pk_value',
            $this->getConnection()->quoteInto('main_table.entity_pk_value=?', $pkValue),
            'string');

        return $this;
    }

    public function addEntityInfo($entityName)
    {

        return $this;
    }

    /**
     * Add status filter
     *
     * @param   int|string $status
     * @return  Varien_Data_Collection_Db
     */
    public function addStatusFilter($status)
    {
        if (is_numeric($status)) {
            $this->addFilter('status',
                $this->getConnection()->quoteInto('main_table.status_id=?', $status),
                'string');
        }
        elseif (is_string($status)) {
            $this->_select->join($this->_reviewStatusTable,
                'main_table.status_id='.$this->_reviewStatusTable.'.status_id');

            $this->addFilter('status',
                $this->getConnection()->quoteInto($this->_reviewStatusTable.'.status_code=?', $status),
                'string');
        }
        return $this;
    }

    public function setDateOrder($dir='DESC')
    {
        $this->setOrder('main_table.created_at', $dir);
        return $this;
    }

    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($item->getId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->load();
            $item->setRatingVotes( $votesCollection );
        }

        return $this;
    }

    public function addReviewsTotalCount()
    {
        $this->_select->joinLeft(array('r' => $this->_reviewTable), 'main_table.entity_pk_value = r.entity_pk_value', 'COUNT(r.review_id) as total_reviews');
        $this->_select->group('main_table.review_id');

        return $this;
    }

    public function load($printQuery=false, $logQuery=false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        Mage::dispatchEvent('review_review_collection_load_before', array('collection' => $this));
        parent::load($printQuery, $logQuery);
        if($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }



        return $this;
    }

    protected function _addStoreData()
    {
        $reviewsIds = $this->getColumnValues('review_id');
        $storesToReviews = array();
        if (count($reviewsIds)>0) {
            $select = $this->getConnection()->select()
                ->from($this->_reviewStoreTable)
                ->where('review_id IN(?)', $reviewsIds);
            $result = $this->getConnection()->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = array();
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if(isset($storesToReviews[$item->getId()])) {
                $item->setStores($storesToReviews[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }
    }
}
