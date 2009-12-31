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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tags customer collection
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_allowDisableGrouping = true;
    protected $_countAttribute = 'tr.tag_relation_id';
    protected $_joinFlags = array();

    public function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        $this->_setIdFieldName('tag_relation_id');
        return $this;
    }

    public function setJoinFlag($table)
    {
        $this->_joinFlags[$table] = true;
        return $this;
    }

    public function getJoinFlag($table)
    {
        return isset($this->_joinFlags[$table]);
    }

    public function unsetJoinFlag($table=null)
    {
        if (is_null($table)) {
            $this->_joinFlags = array();
        } elseif ($this->getJoinFlag($table)) {
            unset($this->_joinFlags[$table]);
        }

        return $this;
    }

    public function addTagFilter($tagId)
    {
        $this->getSelect()
            ->where('tr.tag_id = ?', $tagId);
        return $this;
    }

    public function addProductFilter($productId)
    {
        $this->getSelect()
            ->where('tr.product_id = ?', $productId);
        return $this;
    }

    public function addStatusFilter($status)
    {
        $this->getSelect()
            ->where('t.status = ?', $status);
        return $this;
    }

    public function addDescOrder()
    {
        $this->getSelect()
            ->order('tr.tag_relation_id desc');
        return $this;
    }

    public function addGroupByTag()
    {
        $this->getSelect()
            ->group('tr.tag_relation_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function addGroupByCustomer()
    {
        $this->getSelect()
            ->group('tr.customer_id');

        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function addGroupByCustomerProduct()
    {
        // Nothing need to group
        $this->_allowDisableGrouping = false;
        return $this;
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('tr.customer_id = ?', $customerId);
        return $this;
    }

    protected function _joinFields()
    {
        $tagRelationTable = $this->getTable('tag/relation');
        $tagTable = $this->getTable('tag/tag');

        //TODO: add full name logic
        $this->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addAttributeToSelect('email');

        $this->getSelect()
            ->join(array('tr' => $tagRelationTable), 'tr.customer_id = e.entity_id')
            ->join(array('t' => $tagTable), 't.tag_id = tr.tag_id');
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        if( $this->_allowDisableGrouping ) {
            $countSelect->reset(Zend_Db_Select::GROUP);
        }

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', "select count({$this->getCountAttribute()}) from ", $sql);
        return $sql;
    }

    public function addProductName()
    {
        $productsId = array();
        $productsData = array();

        foreach ($this->getItems() as $item)
        {
            $productsId[] = $item->getProductId();
        }

        $productsId = array_unique($productsId);

        /* small fix */
        if( sizeof($productsId) == 0 ) {
            return;
        }

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addIdFilter($productsId);

        $collection->load();

        foreach ($collection->getItems() as $item)
        {
            $productsData[$item->getId()] = $item->getName();
            $productsSku[$item->getId()] = $item->getSku();
        }

        foreach ($this->getItems() as $item)
        {
            $item->setProduct($productsData[$item->getProductId()]);
            $item->setProductSku($productsSku[$item->getProductId()]);
        }
        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        switch( $attribute ) {
            case 'name':
            case 'status':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;

            default:
                parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    public function setCountAttribute($value)
    {
        $this->_countAttribute = $value;
        return $this;
    }

    public function getCountAttribute()
    {
        return $this->_countAttribute;
    }

    public function addFieldToFilter($attribute, $condition=null){
        if ($attribute == 'name') {
            $this->getSelect()->where($this->_getConditionSql('t.name', $condition));
            return $this;
        } else {
            return parent::addFieldToFilter($attribute, $condition);
        }
    }
}