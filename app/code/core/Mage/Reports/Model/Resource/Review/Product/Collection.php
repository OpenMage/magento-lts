<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report Products Review collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Review_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_useAnalyticFunction = true;
    }

    /**
     * Join review table to result
     *
     * @return $this
     */
    public function joinReview()
    {
        /** @var Mage_Core_Model_Resource_Helper_Mysql4 $helper */
        $helper    = Mage::getResourceHelper('core');

        $subSelect = clone $this->getSelect();
        $subSelect->reset()
            ->from(['rev' => $this->getTable('review/review')], 'COUNT(DISTINCT rev.review_id)')
            ->where('e.entity_id = rev.entity_pk_value');

        $this->addAttributeToSelect('name');

        $this->getSelect()
            ->join(
                ['r' => $this->getTable('review/review')],
                'e.entity_id = r.entity_pk_value',
                [
                    'review_cnt'    => new Zend_Db_Expr(sprintf('(%s)', $subSelect)),
                    'last_created'  => new Zend_Db_Expr('MAX(r.created_at)'),
                ],
            )
            ->group('e.entity_id');

        $joinCondition      = [
            'e.entity_id = table_rating.entity_pk_value',
            $this->getConnection()->quoteInto('table_rating.store_id > ?', 0),
        ];

        $percentField       = $this->getConnection()->quoteIdentifier('table_rating.percent');
        $sumPercentField    = "SUM({$percentField})";
        $sumPercentApproved = 'SUM(table_rating.percent_approved)';
        $countRatingId      = 'COUNT(table_rating.rating_id)';

        $this->getSelect()
            ->joinLeft(
                ['table_rating' => $this->getTable('rating/rating_vote_aggregated')],
                implode(' AND ', $joinCondition),
                [
                    'avg_rating'          => new Zend_Db_Expr("$sumPercentField / $countRatingId"),
                    'avg_rating_approved' => new Zend_Db_Expr("$sumPercentApproved / $countRatingId"),
                ],
            );

        return $this;
    }

    /**
     * Add attribute to sort
     *
     * @inheritDoc
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (in_array($attribute, ['review_cnt', 'last_created', 'avg_rating', 'avg_rating_approved'])) {
            $this->getSelect()->order($attribute . ' ' . $dir);
            return $this;
        }

        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * Get select count sql
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->resetJoinLeft();
        $select->columns(new Zend_Db_Expr('1'));

        $countSelect = clone $select;
        $countSelect->reset();
        $countSelect->from($select, 'COUNT(*)');

        return $countSelect;
    }
}
