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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity attribute option collection
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_optionValueTable;

    public function _construct()
    {
        $this->_init('eav/entity_attribute_option');
        $this->_optionValueTable = Mage::getSingleton('core/resource')->getTableName('eav/attribute_option_value');
    }

    public function setAttributeFilter($setId)
    {
        $this->getSelect()->where('main_table.attribute_id=?', $setId);
        return $this;
    }

    public function setStoreFilter($storeId=null, $useDefaultValue=true)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        if ($useDefaultValue) {
            $this->getSelect()
                ->join(array('store_default_value'=>$this->_optionValueTable),
                    'store_default_value.option_id=main_table.option_id',
                    array('default_value'=>'value'))
                ->joinLeft(array('store_value'=>$this->_optionValueTable),
                    'store_value.option_id=main_table.option_id AND '.$this->getConnection()->quoteInto('store_value.store_id=?', $storeId),
                    array('store_value'=>'value',
                    'value' => new Zend_Db_Expr('IFNULL(store_value.value,store_default_value.value)')))
                ->where($this->getConnection()->quoteInto('store_default_value.store_id=?', 0));
        }
        else {
            $this->getSelect()
                ->joinLeft(array('store_value'=>$this->_optionValueTable),
                    'store_value.option_id=main_table.option_id AND '.$this->getConnection()->quoteInto('store_value.store_id=?', $storeId),
                    'value')
                ->where($this->getConnection()->quoteInto('store_value.store_id=?', $storeId));
        }

        return $this;
    }

    public function setIdFilter($id)
    {
        if (is_array($id)) {
            $this->getSelect()->where('main_table.option_id IN (?)', $id);
        }
        else {
            $this->getSelect()->where('main_table.option_id=?', $id);
        }
        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray('option_id', 'value');
    }

    public function setPositionOrder($dir='asc')
    {
        $this->setOrder('main_table.sort_order', $dir);
        return $this;
    }
}
