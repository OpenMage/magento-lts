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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tag Relation resource model
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Tag_Relation extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/relation', 'tag_relation_id');
    }

    public function loadByTagCustomer($model)
    {
        if( $model->getTagId() && $model->getCustomerId() ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();

            $select->from($this->getMainTable())
                ->join($this->getTable('tag/tag'), "{$this->getTable('tag/tag')}.tag_id = {$this->getMainTable()}.tag_id")
                ->where("{$this->getMainTable()}.tag_id = ?", $model->getTagId())
                ->where('customer_id = ?', $model->getCustomerId());

            if( $model->getProductId() ) {
                $select->where("{$this->getMainTable()}.product_id = ?", $model->getProductId());
            }

            if( $model->hasStoreId() ) {
                $select->where("{$this->getMainTable()}.store_id = ?", $model->getStoreId());
            }

            $data = $read->fetchRow($select);
            $model->setData( ( is_array($data) ) ? $data : array() );
            return $this;
        } else {
            return $this;
        }
    }

    public function getProductIds($model)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'product_id')
            ->where("tag_id = ?", $model->getTagId())
            ->where('customer_id=?', $model->getCustomerId())
            ->where('active=1');
        if( $model->hasStoreId() ) {
            $select->where('store_id = ?', $model->getStoreId());
        }
        return $this->_getReadAdapter()->fetchCol($select);
    }

    public function deactivate($tagId, $customerId)
    {
        $condition = $this->_getWriteAdapter()->quoteInto('tag_id = ?', $tagId) . ' AND ';
        $condition.= $this->_getWriteAdapter()->quoteInto('customer_id = ?', $customerId);
        $data = array('active'=>0);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
        return $this;
    }
}