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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Report Products Review collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Review_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
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
        $helper    = Mage::getResourceHelper('core');

        $subSelect = clone $this->getSelect();
        $subSelect->reset()
            ->from(array('rev' => $this->getTable('review/review')), 'COUNT(DISTINCT rev.review_id)')
            ->where('e.entity_id = rev.entity_pk_value');

        $this->addAttributeToSelect('name');

        $this->getSelect()
            ->join(
                array('r' => $this->getTable('review/review')),
                'e.entity_id = r.entity_pk_value',
                array(
                    'review_cnt'    => new Zend_Db_Expr(sprintf('(%s)', $subSelect)),
                    'last_created'  => 'MAX(r.created_at)',))
            ->group('e.entity_id');

        $joinCondition      = array(
            'e.entity_id = table_rating.entity_pk_value',
            $this->getConnection()->quoteInto('table_rating.store_id > ?', 0)
        );

        /**
         * @var $groupByCondition array of group by fields
         */
        $groupByCondition   = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
        $percentField       = $this->getConnection()->quoteIdentifier('table_rating.percent');
        $sumPercentField    = $helper->prepareColumn("SUM({$percentField})", $groupByCondition);
        $sumPercentApproved = $helper->prepareColumn('SUM(table_rating.percent_approved)', $groupByCondition);
        $countRatingId      = $helper->prepareColumn('COUNT(table_rating.rating_id)', $groupByCondition);

        $this->getSelect()
            ->joinLeft(
                array('table_rating' => $this->getTable('rating/rating_vote_aggregated')),
                implode(' AND ', $joinCondition),
                array(
                    'avg_rating'          => sprintf('%s/%s', $sumPercentField, $countRatingId),
                    'avg_rating_approved' => sprintf('%s/%s', $sumPercentApproved, $countRatingId),
            ));

        return $this;
    }

    /**
     * Add attribute to sort
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (in_array($attribute, array('review_cnt', 'last_created', 'avg_rating', 'avg_rating_approved'))) {
            $this->getSelect()->order($attribute.' '.$dir);
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

        /* @var Varien_Db_Select $select */
        $select = clone $this->getSelect();
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->resetJoinLeft();
        $select->columns(new Zend_Db_Expr('1'));

        /* @var Varien_Db_Select $countSelect */
        $countSelect = clone $select;
        $countSelect->reset();
        $countSelect->from($select, "COUNT(*)");

        return $countSelect;
    }
}
