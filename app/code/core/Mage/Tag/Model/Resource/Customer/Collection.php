<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tags customer collection
 *
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * Allows disabling grouping
     *
     * @var bool
     */
    protected $_allowDisableGrouping     = true;

    /**
     * Count attribute for count sql
     *
     * @var string
     */
    protected $_countAttribute           = 'tr.tag_id';

    /**
     * Array with joined tables
     *
     * @var array
     */
    protected $_joinFlags                = [];

    /**
     * Prepare select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        $this->_setIdFieldName('tag_relation_id');
        return $this;
    }

    /**
     * Set flag about joined table.
     * setFlag method must be used in future.
     *
     * @param  string $table
     * @return $this
     * @deprecated after 1.3.2.3
     */
    public function setJoinFlag($table)
    {
        $this->setFlag($table, true);
        return $this;
    }

    /**
     * Get flag's status about joined table.
     * getFlag method must be used in future.
     *
     * @param  string $table
     * @return bool
     * @deprecated after 1.3.2.3
     */
    public function getJoinFlag($table)
    {
        return $this->getFlag($table);
    }

    /**
     * Unset value of join flag.
     * Set false (bool) value to flag instead in future.
     *
     * @param  string $table
     * @return $this
     * @deprecated after 1.3.2.3
     */
    public function unsetJoinFlag($table = null)
    {
        $this->setFlag($table, false);
        return $this;
    }

    /**
     * Adds filter by tag is
     *
     * @param  int   $tagId
     * @return $this
     */
    public function addTagFilter($tagId)
    {
        $this->getSelect()
            ->where('tr.tag_id = ?', $tagId);
        return $this;
    }

    /**
     * adds filter by product id
     *
     * @param  int   $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $this->getSelect()
            ->where('tr.product_id = ?', $productId);
        return $this;
    }

    /**
     * Apply filter by store id(s).
     *
     * @param  array|int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()->where('tr.store_id IN (?)', $storeId);
        return $this;
    }

    /**
     * Adds filter by status
     *
     * @param  int   $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('t.status = ?', $status);
        return $this;
    }

    /**
     * Adds desc order by tag relation id
     *
     * @return $this
     */
    public function addDescOrder()
    {
        $this->getSelect()
            ->order('tr.tag_relation_id desc');
        return $this;
    }

    /**
     * Adds grouping by tag id
     *
     * @return $this
     */
    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('tr.tag_id');

        /*
         * Allow analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        $this->_allowDisableGrouping = true;
        return $this;
    }

    /**
     * Adds grouping by customer id
     *
     * @return $this
     */
    public function addGroupByCustomer()
    {
        $this->getSelect()
            ->group('tr.customer_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    /**
     * Disables grouping
     *
     * @return $this
     */
    public function addGroupByCustomerProduct()
    {
        // Nothing need to group
        $this->_allowDisableGrouping = false;
        return $this;
    }

    /**
     * Adds filter by customer id
     *
     * @param  int   $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('tr.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Joins tables to select
     */
    protected function _joinFields()
    {
        $tagRelationTable = $this->getTable('tag/relation');
        $tagTable = $this->getTable('tag/tag');

        //TODO: add full name logic
        $this->addAttributeToSelect('firstname')
            ->addAttributeToSelect('middlename')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');

        $this->getSelect()
        ->join(
            ['tr' => $tagRelationTable],
            'tr.customer_id = e.entity_id',
            ['tag_relation_id', 'product_id', 'active', 'added_in' => 'store_id'],
        )
        ->join(['t' => $tagTable], 't.tag_id = tr.tag_id', ['*']);
    }

    /**
     * Gets number of rows
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();

        if ($this->_allowDisableGrouping) {
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT ' . $this->getCountAttribute() . ')');
        }

        return $countSelect;
    }

    /**
     * Adds Product names to item
     *
     * @return $this
     */
    public function addProductName()
    {
        $productsId   = [];
        $productsSku  = [];
        $productsData = [];

        foreach ($this->getItems() as $item) {
            $productsId[] = $item->getProductId();
        }

        $productsId = array_unique($productsId);

        /* small fix */
        if ($productsId === []) {
            return $this;
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addIdFilter($productsId);

        $collection->load();

        foreach ($collection->getItems() as $item) {
            $productsData[$item->getId()] = $item->getName();
            $productsSku[$item->getId()] = $item->getSku();
        }

        foreach ($this->getItems() as $item) {
            $item->setProduct($productsData[$item->getProductId()]);
            $item->setProductSku($productsSku[$item->getProductId()]);
        }

        return $this;
    }

    /**
     * Adds Product names to select
     *
     * @return $this
     */
    public function addProductToSelect()
    {
        $resource = Mage::getModel('catalog/product')->getResource();

        // add product attributes to select
        foreach (['name' => 'value'] as $field => $fieldName) {
            $attr = $resource->getAttribute($field);
            $this->_select->joinLeft(
                [$field => $attr->getBackend()->getTable()],
                'tr.product_id = ' . $field . '.entity_id AND ' . $field . '.attribute_id = ' . $attr->getId(),
                ['product_' . $field => $fieldName],
            );
        }

        // add product fields
        $this->_select->joinLeft(
            ['p' => $this->getTable('catalog/product')],
            'tr.product_id = p.entity_id',
            ['product_sku' => 'sku'],
        );

        return $this;
    }

    /**
     * Sets attribute for count
     *
     * @param  string $value
     * @return $this
     */
    public function setCountAttribute($value)
    {
        $this->_countAttribute = $value;
        return $this;
    }

    /**
     * Gets attribure for count
     *
     * @return string
     */
    public function getCountAttribute()
    {
        return $this->_countAttribute;
    }

    /**
     * Adds field to filter
     *
     * @inheritDoc
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        if ($attribute == 'name') {
            $where = $this->_getConditionSql('t.name', $condition);
            $this->getSelect()->where($where, null, Varien_Db_Select::TYPE_CONDITION);
            return $this;
        }

        return parent::addFieldToFilter($attribute, $condition);
    }

    /**
     * Treat "order by" items as attributes to sort
     *
     * @return $this
     */
    protected function _renderOrders()
    {
        if (!$this->_isOrdersRendered) {
            parent::_renderOrders();

            $orders = $this->getSelect()
                ->getPart(Zend_Db_Select::ORDER);

            $appliedOrders = [];
            foreach ($orders as $order) {
                $appliedOrders[$order[0]] = true;
            }

            foreach ($this->_orders as $field => $direction) {
                if (empty($appliedOrders[$field])) {
                    $this->_select->order(new Zend_Db_Expr($field . ' ' . $direction));
                }
            }
        }

        return $this;
    }
}
