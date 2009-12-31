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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review Product Collection
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Review_Model_Mysql4_Review_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    protected $_entitiesAlias = array();
    protected $_reviewStoreTable;
    protected $_addStoreDataFlag = false;

    protected function _construct()
    {
        $this->_init('catalog/product');
        $this->setRowIdFieldName('review_id');
        $this->_reviewStoreTable = Mage::getSingleton('core/resource')->getTableName('review/review_store');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    public function addStoreFilter($storeId=null)
    {
        parent::addStoreFilter($storeId);
        $this->getSelect()
            ->join(array('store'=>$this->_reviewStoreTable),
                'rt.review_id=store.review_id AND store.store_id=' . (int)$storeId, array());
        return $this;
    }

    public function setStoreFilter($storeId)
    {
        if( is_array($storeId) && isset($storeId['eq']) ) {
            $storeId = array_shift($storeId);
        }

        if( is_array($storeId) ) {
            $this->getSelect()
                ->join(array('store'=>$this->_reviewStoreTable),
                    $this->getConnection()->quoteInto('rt.review_id=store.review_id AND store.store_id IN(?)', $storeId), array())
                ->distinct(true)
                ;
        } else {
            $this->getSelect()
                ->join(array('store'=>$this->_reviewStoreTable),
                    'rt.review_id=store.review_id AND store.store_id=' . (int)$storeId, array());
        }

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
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('rdt.customer_id = ?', $customerId);
        return $this;
    }

    public function addEntityFilter($entityId)
    {
        $this->getSelect()
            ->where('rt.entity_pk_value = ?', $entityId);
        return $this;
    }

    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('rt.status_id = ?', $status);
        return $this;
    }

    public function setDateOrder($dir='DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }

    public function addReviewSummary()
    {
        foreach( $this->getItems() as $item ) {
            $model = Mage::getModel('rating/rating');
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($item->getEntityId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load();
            $item->setRatingVotes( $votesCollection );
        }
        return $this;
    }

    protected function _joinFields()
    {
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->getSelect()
            ->join(array('rt' => $reviewTable),
                'rt.entity_pk_value = e.entity_id',
                array('review_id', 'created_at', 'entity_pk_value', 'status_id'))
            ->join(array('rdt' => $reviewDetailTable), 'rdt.review_id = rt.review_id');
        return $this;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->from(null, 'rt.review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }


    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(e.entity_id) from ', $sql);

        return $sql;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            case 'stores':
                // No way to sort
                break;
            case 'type':
                $this->getSelect()->order('rdt.customer_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function addAttributeToFilter($attribute, $condition=null, $joinType='inner')
    {
        switch( $attribute ) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                return $this;
                break;
           case 'stores':
                $this->setStoreFilter($condition);
                return $this;
                break;
            case 'type':
                if($condition == 1) {
                	$this->getSelect()->where('rdt.customer_id = 0');
                } elseif ($condition == 2) {
                	$this->getSelect()->where('rdt.customer_id > 0');
                } else {
                    $this->getSelect()->where('rdt.customer_id IS NULL');
                }
                return $this;
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
        return $this;
    }

    public function getColumnValues($colName)
    {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
    	if ($this->_addStoreDataFlag) {
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
                ->where('review_id IN(?)', $reviewsIds)
                ->where('store_id > ?', 0);
            $result = $this->getConnection()->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = array();
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if(isset($storesToReviews[$item->getReviewId()])) {
                $item->setData('stores',$storesToReviews[$item->getReviewId()]);
            } else {
                $item->setData('stores', array());
            }

        }
    }
 }
