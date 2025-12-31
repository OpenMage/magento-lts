<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report Products Tags collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Tag_Product_Collection extends Mage_Tag_Model_Resource_Product_Collection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        /**
         * Allow to use analytic function
         */
        $this->_useAnalyticFunction = true;
    }

    /**
     * Add unique target count to result
     *
     * @return $this
     */
    public function addUniqueTagedCount()
    {
        $select = clone $this->getSelect();

        $select->reset()
            ->from(['rel' => $this->getTable('tag/relation')], 'COUNT(DISTINCT rel.tag_id)')
            ->where('rel.product_id = e.entity_id');

        $this->getSelect()
            ->columns(['utaged' => new Zend_Db_Expr(sprintf('(%s)', $select))]);
        return $this;
    }

    /**
     * Add all target count to result
     *
     * @return $this
     */
    public function addAllTagedCount()
    {
        $this->getSelect()
            ->columns(['taged' => 'COUNT(relation.tag_id)']);
        return $this;
    }

    /**
     * Add target count to result
     *
     * @return $this
     */
    public function addTagedCount()
    {
        $this->getSelect()
            ->columns(['taged' => 'COUNT(relation.tag_relation_id)']);

        return $this;
    }

    /**
     * Add group by product to result
     *
     * @return $this
     */
    public function addGroupByProduct()
    {
        $this->getSelect()
            ->group('relation.product_id');
        $this->setJoinFlag('distinct');
        return $this;
    }

    /**
     * Add group by tag to result
     *
     * @return $this
     */
    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('relation.tag_id');
        $this->setJoinFlag('distinct');
        $this->setJoinFlag('group_tag');
        return $this;
    }

    /**
     * Add product filter
     *
     * @param  int   $customerId
     * @return $this
     */
    public function addProductFilter($customerId)
    {
        $this->getSelect()
             ->where('relation.product_id = ?', (int) $customerId);
        $this->_customerFilterId = (int) $customerId;
        return $this;
    }

    /**
     * Set order
     *
     * @param  string $attribute
     * @param  string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        if (in_array($attribute, ['utaged', 'taged', 'tag_name'])) {
            $this->getSelect()->order($attribute . ' ' . $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }

    /**
     * Join fields
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $this->addAttributeToSelect('name');
        $this->getSelect()
            ->join(
                ['relation' => $this->getTable('tag/relation')],
                'relation.product_id = e.entity_id',
                [],
            )
            ->join(
                ['t' => $this->getTable('tag/tag')],
                't.tag_id = relation.tag_id',
                ['tag_id',  'status', 'tag_name' => 'name'],
            );

        return $this;
    }
}
