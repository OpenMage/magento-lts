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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store group collection
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Model_Mysql4_Store_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_loadDefault = false;

    protected function _construct()
    {
        $this->_init('core/store_group');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->_loadDefault) {
            $this->setWithoutDefaultFilter();
        }
        $this->addOrder('main_table.name', 'ASC');
        return parent::load($printQuery, $logQuery);
    }

    public function setLoadDefault($loadDefault)
    {
        $this->_loadDefault = (bool)$loadDefault;
        return $this;
    }

    public function getLoadDefault()
    {
        return $this->_loadDefault;
    }

    public function setWithoutDefaultFilter()
    {
        $this->getSelect()->where($this->getConnection()->quoteInto('main_table.group_id>?', 0));
        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('group_id', 'name');
    }

    public function addWebsiteFilter($website)
    {
        if (is_array($website)) {
            $condition = $this->getConnection()->quoteInto('main_table.website_id IN(?)', $website);
        }
        else {
            $condition = $this->getConnection()->quoteInto('main_table.website_id=?', $website);
        }
        return $this->addFilter('website_id', $condition, 'string');
    }
}
