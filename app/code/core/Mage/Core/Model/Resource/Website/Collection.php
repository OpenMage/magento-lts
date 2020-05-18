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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Websites collection
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Website_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @deprecated since 1.5.0.0
     */
    protected $_loadDefault    = false;

    /**
     * Map field to alias
     *
     * @var array
     */
    protected $_map = array('fields' => array('website_id' => 'main_table.website_id'));

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->setFlag('load_default_website', false);
        $this->_init('core/website');
    }

    /**
     * Set flag for load default (admin) website
     *
     * @param boolean $loadDefault
     * @return $this
     */
    public function setLoadDefault($loadDefault)
    {
        $this->setFlag('load_default_website', (bool)$loadDefault);
        return $this;
    }

    /**
     * Is load default (admin) website
     *
     * @return boolean
     */
    public function getLoadDefault()
    {
        return $this->getFlag('load_default_website');
    }

    /**
     * Convert items array to array for select options
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('website_id', 'name');
    }

    /**
     * Convert items array to hash for select options
     *
     * @return Array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('website_id', 'name');
    }


    /**
     * Add website filter to collection
     *
     * @param int $ids|array
     * @return $this
     */
    public function addIdFilter($ids)
    {
        if (is_array($ids)) {
            if (empty($ids)) {
                $this->addFieldToFilter('website_id', null);
            } else {
                $this->addFieldToFilter('website_id', array('in' => $ids));
            }
        } else {
            $this->addFieldToFilter('website_id', $ids);
        }
        return $this;
    }

    /**
     * Load collection data
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->getLoadDefault()) {
            $this->getSelect()->where('main_table.website_id > ?', 0);
        }
        $this->unshiftOrder('main_table.name', Varien_Db_Select::SQL_ASC)       // website name SECOND
             ->unshiftOrder('main_table.sort_order', Varien_Db_Select::SQL_ASC); // website sort order FIRST

        return parent::load($printQuery, $logQuery);

    }

    /**
     * Join group and store info from appropriate tables.
     * Defines new _idFiledName as 'website_group_store' bc for
     * one website can be more then one row in collection.
     * Sets extra combined ordering by group's name, defined
     * sort ordering and store's name.
     *
     * @return $this
     */
    public function joinGroupAndStore()
    {
        if (!$this->getFlag('groups_and_stores_joined')) {
            $this->_idFieldName = 'website_group_store';
            $this->getSelect()->joinLeft(
                array('group_table' => $this->getTable('core/store_group')),
                'main_table.website_id = group_table.website_id',
                array('group_id' => 'group_id', 'group_title' => 'name')
            )->joinLeft(
                array('store_table' => $this->getTable('core/store')),
                'group_table.group_id = store_table.group_id',
                array('store_id' => 'store_id', 'store_title' => 'name')
            );
            $this->addOrder('group_table.name', Varien_Db_Select::SQL_ASC)       // store name
                ->addOrder('CASE WHEN store_table.store_id = 0 THEN 0 ELSE 1 END', Varien_Db_Select::SQL_ASC) // view is admin
                ->addOrder('store_table.sort_order', Varien_Db_Select::SQL_ASC) // view sort order
                ->addOrder('store_table.name', Varien_Db_Select::SQL_ASC)       // view name
            ;
            $this->setFlag('groups_and_stores_joined', true);
        }
        return $this;
    }

    /**
     * Adding filter by group id or array of ids but only if
     * tables with appropriate information were joined before.
     *
     * @param int|array $groupIds
     * @return $this
     */
    public function addFilterByGroupIds($groupIds)
    {
        if ($this->getFlag('groups_and_stores_joined')) {
            $this->addFieldToFilter('group_table.group_id', $groupIds);
        }
        return $this;
    }
}
