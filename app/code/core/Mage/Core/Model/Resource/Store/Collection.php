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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Stores collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Store_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Load default flag
     *
     * @deprecated since 1.5.0.0
     * @var boolean
     */
    protected $_loadDefault    = false;

    /**
     *  Define resource model
     *
     */
    protected function _construct()
    {
        $this->setFlag('load_default_store', false);
        $this->_init('core/store');
    }

    /**
     * Set flag for load default (admin) store
     *
     * @param boolean $loadDefault
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function setLoadDefault($loadDefault)
    {
        $this->setFlag('load_default_store', (bool)$loadDefault);
        return $this;
    }

    /**
     * Is load default (admin) store
     *
     * @return boolean
     */
    public function getLoadDefault()
    {
        return $this->getFlag('load_default_store');
    }

    /**
     * Add disable default store filter to collection
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function setWithoutDefaultFilter()
    {
        $this->addFieldToFilter('main_table.store_id', array('gt' => 0));
        return $this;
    }

    /**
     * Add filter by group id.
     * Group id can be passed as one single value or array of values.
     *
     * @param int|array $groupId
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function addGroupFilter($groupId)
    {
        return $this->addFieldToFilter('main_table.group_id', array('in' => $groupId));
    }

    /**
     * Add store id(s) filter to collection
     *
     * @param int|array $store
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function addIdFilter($store)
    {
        return $this->addFieldToFilter('main_table.store_id', array('in' => $store));
    }

    /**
     * Add filter by website to collection
     *
     * @param int|array $website
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function addWebsiteFilter($website)
    {
        return $this->addFieldToFilter('main_table.website_id', array('in' => $website));
    }

    /**
     * Add root category id filter to store collection
     *
     * @param int|array $category
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function addCategoryFilter($category)
    {
        if (!is_array($category)) {
            $category = array($category);
        }
        return $this->loadByCategoryIds($category);
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('store_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('store_id', 'name');
    }

    /**
     * Load collection data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->getLoadDefault()) {
            $this->setWithoutDefaultFilter();
        }

        if (!$this->isLoaded()) {
            $this->addOrder('CASE WHEN main_table.store_id = 0 THEN 0 ELSE 1 END', Varien_Db_Select::SQL_ASC)
                ->addOrder('main_table.sort_order', Varien_Db_Select::SQL_ASC)
                ->addOrder('main_table.name', Varien_Db_Select::SQL_ASC);
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Add root category id filter to store collection
     *
     * @param array $categories
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function loadByCategoryIds(array $categories)
    {
        $this->addRootCategoryIdAttribute();
        $this->addFieldToFilter('group_table.root_category_id', array('in' => $categories));

        return $this;
    }

    /**
     * Add store root category data to collection
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function addRootCategoryIdAttribute()
    {
        if (!$this->getFlag('core_store_group_table_joined')) {
            $this->getSelect()->join(
                array('group_table' => $this->getTable('core/store_group')),
                'main_table.group_id = group_table.group_id',
                array('root_category_id')
            );
            $this->setFlag('core_store_group_table_joined', true);
        }

        return $this;
    }
}
