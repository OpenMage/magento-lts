<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category flat collection
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Flat_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix    = 'catalog_category_collection';

    /**
     * @var string
     */
    protected $_eventObject    = 'category_collection';

    /**
     * Store id of application
     *
     * @var int|null
     */
    protected $_storeId        = null;

    /**
     * Catalog factory instance
     *
     * @var Mage_Catalog_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize factory
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     */
    public function __construct($resource = null, array $args = [])
    {
        parent::__construct($resource);
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('catalog/factory');
    }

    /**
     *  Collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/category_flat');
        $this->setModel('catalog/category');
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainStoreTable($this->getStoreId())],
            ['entity_id', 'level', 'path', 'position', 'is_active', 'is_anchor'],
        );
        return $this;
    }

    /**
     * Add filter by entity id(s).
     *
     * @param mixed $categoryIds
     * @return $this
     */
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = ['in' => $categoryIds];
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if (isset($condition)) {
            $this->addFieldToFilter('entity_id', $condition);
        }

        return $this;
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id.
     * If store id is not set yet, return store of application
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            return Mage::app()->getStore()->getId();
        }

        return $this->_storeId;
    }

    /**
     * Add filter by path to collection
     *
     * @param string $parent
     * @return $this
     */
    public function addParentPathFilter($parent)
    {
        $this->addFieldToFilter('path', ['like' => "{$parent}/%"]);
        return $this;
    }

    /**
     * Add store filter
     *
     * @return $this
     */
    public function addStoreFilter()
    {
        $this->addFieldToFilter('main_table.store_id', $this->getStoreId());
        return $this;
    }

    /**
     * Set field to sort by
     *
     * @param string $sorted
     * @return $this
     */
    public function addSortedField($sorted)
    {
        if (is_string($sorted)) {
            $this->addOrder($sorted, self::SORT_ORDER_ASC);
        } else {
            $this->addOrder('name', self::SORT_ORDER_ASC);
        }

        return $this;
    }

    /**
     * Add active category filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->addFieldToFilter('is_active', 1);
        Mage::dispatchEvent(
            $this->_eventPrefix . '_add_is_active_filter',
            [$this->_eventObject => $this],
        );
        return $this;
    }

    /**
     * Add name field to result
     *
     * @return $this
     */
    public function addNameToResult()
    {
        $this->addAttributeToSelect('name');
        return $this;
    }

    /**
     * Add attribute to select
     *
     * @param array|string $attribute
     * @return $this
     */
    public function addAttributeToSelect($attribute = '*')
    {
        if ($attribute == '*') {
            // Save previous selected columns
            $columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
            $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
            foreach ($columns as $column) {
                if ($column[0] == 'main_table') {
                    // If column selected from main table,
                    // no need to select it again
                    continue;
                }

                // Joined columns
                if ($column[2] !== null) {
                    $expression = [$column[2] => $column[1]];
                } else {
                    $expression = $column[2];
                }

                $this->getSelect()->columns($expression, $column[0]);
            }

            $this->getSelect()->columns('*', 'main_table');
            return $this;
        }

        if (!is_array($attribute)) {
            $attribute = [$attribute];
        }

        $this->getSelect()->columns($attribute, 'main_table');
        return $this;
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if (!is_string($attribute)) {
            return $this;
        }

        $this->setOrder($attribute, $dir);
        return $this;
    }

    /**
     * Emulate simple add attribute filter to collection
     *
     * @param string $attribute
     * @param mixed $condition
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        if (!is_string($attribute) || $condition === null) {
            return $this;
        }

        return $this->addFieldToFilter($attribute, $condition);
    }

    /**
     * Join request_path column from url rewrite table
     *
     * @return $this
     */
    public function addUrlRewriteToResult()
    {
        /** @var Mage_Catalog_Helper_Category_Url_Rewrite_Interface $urlRewrite */
        $urlRewrite = $this->_factory->getCategoryUrlRewriteHelper();
        $urlRewrite->joinTableToCollection($this, $this->_getCurrentStoreId());

        return $this;
    }

    /**
     * Join request_path column from url rewrite table
     */
    public function joinUrlRewrite()
    {
        return $this->addUrlRewriteToResult();
    }

    /**
     * Retrieves store_id from current store
     *
     * @return int
     */
    protected function _getCurrentStoreId()
    {
        return (int) Mage::app()->getStore()->getId();
    }

    /**
     * Add category path filter
     *
     * @param array|string $paths
     * @return $this
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        $select = $this->getSelect();
        $cond   = [];
        foreach ($paths as $path) {
            $cond[] = $this->getResource()->getReadConnection()->quoteInto('main_table.path LIKE ?', "$path%");
        }

        if ($cond) {
            $select->where(implode(' OR ', $cond));
        }

        return $this;
    }

    /**
     * Add category level filter
     *
     * @param int $level
     * @return $this
     */
    public function addLevelFilter($level)
    {
        $this->getSelect()->where('main_table.level <= ?', $level);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field
     * @return $this
     */
    public function addOrderField($field)
    {
        $this->setOrder('main_table.' . $field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Set collection page start and records to show
     *
     * @param int $pageNum
     * @param int $pageSize
     * @return $this
     */
    public function setPage($pageNum, $pageSize)
    {
        $this->setCurPage($pageNum)
            ->setPageSize($pageSize);
        return $this;
    }
}
