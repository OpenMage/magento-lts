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
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Reviews collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Model_Mysql4_Review_Collection extends Mage_Review_Model_Mysql4_Review_Collection
{
    protected function _construct()
    {
        $this->_init('review/review');
    }

    public function addProductFilter($productId)
    {
        $this->_select
            ->where('main_table.entity_pk_value = ?', $productId);

        return $this;
    }

    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();

        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(main_table.review_id) from ', $sql);

        return $sql;
    }

    public function setOrder($attribute, $dir='desc')
    {
        $fields = array(
            'nickname',
            'title',
            'detail',
            'created_at'
        );

        if (in_array($attribute, $fields)) {
                $this->_select->order($attribute . ' ' . $dir);
        } else {
                parent::setOrder($attribute, $dir);
        }

        return $this;
    }

}
