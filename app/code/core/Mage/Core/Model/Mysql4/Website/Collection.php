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
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Websites collection
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Website_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_loadDefault = false;

    protected $_map = array('fields' => array('website_id' => 'main_table.website_id'));

    protected function _construct()
    {
        $this->_init('core/website');
    }

    public function addIdFilter($ids)
    {
        if (is_array($ids)) {
            if (empty($ids)) {
                $this->addFieldToFilter('website_id', null);
            }
            else {
                $this->addFieldToFilter('website_id', array('in'=>$ids));
            }
        }
        else {
            $this->addFieldToFilter('website_id', $ids);
        }
        return $this;
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

    public function toOptionArray()
    {
        return $this->_toOptionArray('website_id', 'name');
    }

    public function toOptionHash()
    {
        return $this->_toOptionHash('website_id', 'name');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->getLoadDefault()) {
            $this->getSelect()->where($this->getConnection()->quoteInto('main_table.website_id>?', 0));
        }
        $this->unshiftOrder('main_table.name', 'ASC')       // website name SECOND
            ->unshiftOrder('main_table.sort_order', 'ASC') // website sort order FIRST
        ;
        parent::load($printQuery, $logQuery);
        return $this;
    }

    /**
     * Join group and store info from appropriate tables.
     * Defines new _idFiledName as 'website_group_store' bc for
     * one website can be more then one row in collection.
     * Sets extra combined ordering by group's name, defined
     * sort ordering and store's name.
     *
     * @return Mage_Core_Model_Mysql4_Website_Collection
     */
    public function joinGroupAndStore()
    {
        if (!$this->getFlag('groups_and_stores_joined')) {
            $this->_idFieldName = 'website_group_store';
            $this->getSelect()->joinLeft(
                array('group_table' => $this->getTable('core/store_group')),
                'main_table.website_id=group_table.website_id',
                array('group_id'=>'group_id', 'group_title'=>'name')
            )->joinLeft(
                array('store_table' => $this->getTable('core/store')),
                'group_table.group_id=store_table.group_id',
                array('store_id'=>'store_id', 'store_title'=>'name')
            );
            $this->addOrder('group_table.name', 'ASC')       // store name
                ->addOrder('CASE WHEN store_table.store_id = 0 THEN 0 ELSE 1 END', 'ASC') // view is admin
                ->addOrder('store_table.sort_order', 'ASC') // view sort order
                ->addOrder('store_table.name', 'ASC')       // view name
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
     * @return Mage_Core_Model_Mysql4_Website_Collection
     */
    public function addFilterByGroupIds($groupIds)
    {
        if ($this->getFlag('groups_and_stores_joined')) {
            $this->addFieldToFilter('group_table.group_id', $groupIds);
        }
        return $this;
    }
}
