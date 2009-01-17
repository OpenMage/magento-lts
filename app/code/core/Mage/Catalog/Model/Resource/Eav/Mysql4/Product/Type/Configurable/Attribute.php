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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog super product attribute resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_labelTable;
    protected $_priceTable;

    protected function _construct()
    {
        $this->_init('catalog/product_super_attribute', 'product_super_attribute_id');
        $this->_labelTable = $this->getTable('catalog/product_super_attribute_label');
        $this->_priceTable = $this->getTable('catalog/product_super_attribute_pricing');
    }

    public function loadLabel($attribute)
    {

    }

    public function loadPrices($attribute)
    {

    }

    public function saveLabel($attribute)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_labelTable, 'value_id')
            ->where('product_super_attribute_id=?', $attribute->getId())
            ->where('store_id=?', (int) $attribute->getStoreId());
        if ($valueId = $this->_getWriteAdapter()->fetchOne($select)) {
            $this->_getWriteAdapter()->update($this->_labelTable,array('value'=>$attribute->getLabel()),
                $this->_getWriteAdapter()->quoteInto('value_id=?', $valueId)
            );
        }
        else {
            $this->_getWriteAdapter()->insert($this->_labelTable, array(
                'product_super_attribute_id' => $attribute->getId(),
                'store_id' => (int) $attribute->getStoreId(),
                'value' => $attribute->getLabel()
            ));
        }
        return $this;
    }

    public function savePrices($attribute)
    {
        $this->_getWriteAdapter()->delete($this->_priceTable,
            $this->_getWriteAdapter()->quoteInto('product_super_attribute_id=?', $attribute->getId())
        );
        $prices = $attribute->getValues();
        foreach ($prices as $data) {
            if(empty($data['pricing_value'])) {
                continue;
            }
        	$priceObject = new Varien_Object($data);
        	$this->_getWriteAdapter()->insert($this->_priceTable, array(
        	   'product_super_attribute_id' => $attribute->getId(),
        	   'value_index' => $priceObject->getValueIndex(),
        	   'is_percent' => $priceObject->getIsPercent(),
        	   'pricing_value' => $priceObject->getPricingValue()
        	));
        }
        return $this;
    }
}