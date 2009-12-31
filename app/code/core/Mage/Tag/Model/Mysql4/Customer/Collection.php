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
 * @package     Mage_Tag
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    protected $_countAttribute = 'tr.tag_id';

    /**
     * @deprecated after 1.3.2.3
     */
    protected $_joinFlags = array();

    public function _initSelect()
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
     * @deprecated after 1.3.2.3
     * @param string $table
     * @return Mage_Tag_Model_Mysql4_Customer_Collection
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
     * @deprecated after 1.3.2.3
     * @param $table
     * @return bool
     */
    public function getJoinFlag($table)
    {
        return $this->getFlag($table);
    }

    /**
     * Unset value of join flag.
     * Set false (bool) value to flag instead in future.
     *
     * @deprecated after 1.3.2.3
     * @param $table
     * @return Mage_Tag_Model_Mysql4_Customer_Collection
     */
    public function unsetJoinFlag($table=null)
    {
        $this->setFlag($table, false);
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

    /**
     * Apply filter by store id(s).
     *
     * @param int|array $storeId
     * @return Mage_Tag_Model_Mysql4_Customer_Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()->where('tr.store_id IN (?)', $storeId);
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
            ->group('tr.tag_id');

        $this->_allowDisableGrouping = true;
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
        $countSelect = parent::getSelectCountSql();

        if ($this->_allowDisableGrouping) {
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT ' . $this->getCountAttribute() . ')');
        }
        return $countSelect;
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
