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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Stores collection
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Store_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_loadDefault = false;

    protected function _construct()
    {
        $this->_init('core/store');
    }

    public function setLoadDefault($loadDefault)
    {
        $this->_loadDefault = $loadDefault;
        return $this;
    }

    public function getLoadDefault()
    {
        return $this->_loadDefault;
    }

    public function setWithoutDefaultFilter()
    {
        $this->getSelect()->where($this->getConnection()->quoteInto('main_table.store_id>?', 0));
        return $this;
    }

    public function addGroupFilter($groupId)
    {
        $condition = $this->getConnection()->quoteInto("group_id=?", $groupId);
        $this->addFilter('group_id', $condition, 'string');
        return $this;
    }

    public function addIdFilter($store)
    {
        if (is_array($store)) {
            $condition = $this->getConnection()->quoteInto("store_id IN (?)", $store);
        }
        else {
            $condition = $this->getConnection()->quoteInto("store_id=?",$store);
        }

        $this->addFilter('store_id', $condition, 'string');
        return $this;
    }

    public function addWebsiteFilter($website)
    {
        if (is_array($website)) {
            $condition = $this->getConnection()->quoteInto("website_id IN (?)", $website);
        }
        else {
            $condition = $this->getConnection()->quoteInto("website_id=?",$website);
        }

        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    public function addCategoryFilter($category)
    {
        if (is_array($category)) {
            $condition = $this->getConnection()->quoteInto("root_category_id IN (?)", $category);
        }
        else {
            $condition = $this->getConnection()->quoteInto("root_category_id=?",$category);
        }

        $this->addFilter('category', $condition, 'string');
        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('store_id', 'name');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('store_id', 'name');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->getLoadDefault()) {
            $this->getSelect()->where($this->getConnection()->quoteInto('main_table.store_id>?', 0));
        }
        $this->getSelect()->order('main_table.sort_order ASC');
        parent::load($printQuery, $logQuery);
        return $this;
    }

    public function loadByCategoryIds(array $categories)
    {
        $this->setLoadDefault(true);
        $condition = $this->getConnection()->quoteInto('root_category_id IN(?)', $categories);
        $this->_select->joinLeft(
            array('group_table' => $this->getTable('core/store_group')),
            'main_table.group_id=group_table.group_id',
            array('root_category_id')
        )->where($condition);

        return $this;
    }

    public function addRootCategoryIdAttribute()
    {
        $this->_select->joinLeft(
            array('group_table' => $this->getTable('core/store_group')),
            'main_table.group_id=group_table.group_id',
            array('root_category_id')
        );
        return $this;
    }
}