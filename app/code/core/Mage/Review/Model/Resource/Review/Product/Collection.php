<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review Product Collection
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Resource_Review_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * Entities alias
     *
     * @var array
     */
    protected $_entitiesAlias        = [];

    /**
     * Review store table
     *
     * @var string
     */
    protected $_reviewStoreTable;

    /**
     * Add store data flag
     *
     * @var bool
     */
    protected $_addStoreDataFlag     = false;

    /**
     * Filter by stores for the collection
     *
     * @var array
     */
    protected $_storesIds           = [];

    /**
     * Define module
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product');
        $this->setRowIdFieldName('review_id');
        $this->_reviewStoreTable = Mage::getSingleton('core/resource')->getTableName('review/review_store');
        $this->_initTables();
    }

    /**
     * init select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Adds store filter into array
     *
     * @param mixed $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }

        parent::addStoreFilter($storeId);

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Adds specific store id into array
     *
     * @param array $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        if (is_array($storeId) && isset($storeId['eq'])) {
            $storeId = array_shift($storeId);
        }

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Applies all store filters in one place to prevent multiple joins in select
     *
     * @param null|Zend_Db_Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select = null)
    {
        $adapter = $this->getConnection();
        $storesIds = $this->_storesIds;
        if (is_null($select)) {
            $select = $this->getSelect();
        }

        if (is_array($storesIds) && (count($storesIds) == 1)) {
            $storesIds = array_shift($storesIds);
        }

        if (is_array($storesIds) && !empty($storesIds)) {
            $inCond = $adapter->prepareSqlCondition('store.store_id', ['in' => $storesIds]);
            $select->join(
                ['store' => $this->_reviewStoreTable],
                'rt.review_id=store.review_id AND ' . $inCond,
                []
            )
            ->group('rt.review_id');

            $this->_useAnalyticFunction = true;
        } else {
            $select->join(
                ['store' => $this->_reviewStoreTable],
                $adapter->quoteInto('rt.review_id=store.review_id AND store.store_id = ?', (int)$storesIds),
                []
            );
        }

        return $this;
    }

    /**
     * Add stores data
     *
     * @return $this
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('rdt.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return $this
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()
            ->where('rt.entity_pk_value = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param mixed $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('rt.status_id = ?', $status);
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return $this
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }

    /**
     * Add review summary
     *
     * @return $this
     */
    public function addReviewSummary()
    {
        foreach ($this->getItems() as $item) {
            $model = Mage::getModel('rating/rating');
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    /**
     * Add rote votes
     *
     * @return $this
     */
    public function addRateVotes()
    {
        foreach ($this->getItems() as $item) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($item->getEntityId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load();
            $item->setRatingVotes($votesCollection);
        }
        return $this;
    }

    /**
     * join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('sku');

        $this->getSelect()
            ->join(
                ['rt' => $reviewTable],
                'rt.entity_pk_value = e.entity_id',
                ['rt.review_id', 'review_created_at' => 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id']
            )
            ->join(
                ['rdt' => $reviewDetailTable],
                'rdt.review_id = rt.review_id',
                ['rdt.title','rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id']
            );
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @inheritDoc
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('rt.review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return Varien_Db_Select|null
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('COUNT(e.entity_id)')
            ->reset(Zend_Db_Select::HAVING);

        return $select;
    }

    /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
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

    /**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch ($attribute) {
            case 'rt.review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            case 'stores':
                $this->setStoreFilter($condition);
                break;
            case 'type':
                if ($condition == 1) {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql('rdt.store_id', ['eq' => Mage_Core_Model_App::ADMIN_STORE_ID])
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                } elseif ($condition == 2) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', ['gt' => 0]);
                } else {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql('rdt.store_id', ['neq' => Mage_Core_Model_App::ADMIN_STORE_ID])
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                }
                $this->getSelect()->where($conditionSql);
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }

    /**
     * Retrieves column values
     *
     * @param string $colName
     * @return array
     */
    public function getColumnValues($colName)
    {
        $col = [];
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    /**
     * Action after load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }

    /**
     * Add store data
     *
     */
    protected function _addStoreData()
    {
        $adapter = $this->getConnection();
        //$this->_getConditionSql('rdt.customer_id', array('null' => null));
        $reviewsIds = $this->getColumnValues('review_id');
        $storesToReviews = [];
        if (count($reviewsIds) > 0) {
            $reviewIdCondition = $this->_getConditionSql('review_id', ['in' => $reviewsIds]);
            $storeIdCondition = $this->_getConditionSql('store_id', ['gt' => 0]);
            $select = $adapter->select()
                ->from($this->_reviewStoreTable)
                ->where($reviewIdCondition)
                ->where($storeIdCondition);
            $result = $adapter->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = [];
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToReviews[$item->getReviewId()])) {
                $item->setData('stores', $storesToReviews[$item->getReviewId()]);
            } else {
                $item->setData('stores', []);
            }
        }
    }

    /**
     * Redeclare parent method for store filters applying
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->_applyStoresFilterToSelect();

        return $this;
    }
}
