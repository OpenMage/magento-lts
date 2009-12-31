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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog super product attribute resource model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Label table name cache
     *
     * @var string
     */
    protected $_labelTable;

    /**
     * Price table name cache
     *
     * @var string
     */
    protected $_priceTable;

    /**
     * Inititalize connection and define tables
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_super_attribute', 'product_super_attribute_id');
        $this->_labelTable = $this->getTable('catalog/product_super_attribute_label');
        $this->_priceTable = $this->getTable('catalog/product_super_attribute_pricing');
    }

    /**
     * Retrieve Catalog Helper
     *
     * @return Mage_Catalog_Helper_Data
     */
    public function getCatalogHelper()
    {
        return Mage::helper('catalog');
    }

    /**
     * Load attribute labels
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute
     */
    public function loadLabel($attribute)
    {
        return $this;
    }

    /**
     * Load prices
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute
     */
    public function loadPrices($attribute)
    {
        return $this;
    }

    /**
     * Save Custom labels for Attribute name
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute
     */
    public function saveLabel($attribute)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_labelTable, 'value_id')
            ->where('product_super_attribute_id=?', $attribute->getId())
            ->where('store_id=?', (int)$attribute->getStoreId());
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

    /**
     * Save Options prices (Depends from price save scope)
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute
     */
    public function savePrices($attribute)
    {
        $newValues      = $attribute->getValues();

        $oldValues      = array();
        $valueIndexes   = array();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_priceTable)
            ->where('product_super_attribute_id=?', $attribute->getId());
        $query  = $this->_getWriteAdapter()->query($select);
        while ($row = $query->fetch()) {
            $key = join('-', array($row['website_id'], $row['value_index']));
            $oldValues[$key] = $row;
        }

        $delete = array();
        $insert = array();
        $update = array();

        foreach ($newValues as $value) {
            $valueIndexes[$value['value_index']] = $value['value_index'];
        }

        if ($this->getCatalogHelper()->isPriceGlobal()) {
            foreach ($oldValues as $row) {
                if (!isset($valueIndexes[$row['value_index']])) {
                    $delete[] = $row['value_id'];
                    continue;
                }
            }
            foreach ($newValues as $value) {
                $valueObject = new Varien_Object($value);
                $key = join('-', array(0, $value['value_index']));

                $pricingValue = $valueObject->getPricingValue();
                if ($pricingValue == '' || is_null($pricingValue)) {
                    $pricingValue = null;
                }
                else {
                    $pricingValue = Mage::app()->getLocale()->getNumber($pricingValue);
                }
                // update
                if (isset($oldValues[$key])) {
                    $oldValue = $oldValues[$key];
                    $update[$oldValue['value_id']] = array(
                        'pricing_value' => $pricingValue,
                        'is_percent'    => intval($valueObject->getIsPercent())
                    );
                }
                // insert
                else {
                    if (!empty($value['pricing_value'])) {
                        $insert[] = array(
                            'product_super_attribute_id'    => $attribute->getId(),
                            'value_index'                   => $valueObject->getValueIndex(),
                            'is_percent'                    => intval($valueObject->getIsPercent()),
                            'pricing_value'                 => $pricingValue,
                            'website_id'                    => 0
                        );
                    }
                }
            }
        }
        else {
            $websiteId = Mage::app()->getStore($attribute->getStoreId())->getWebsiteId();
            foreach ($oldValues as $row) {
                if (!isset($valueIndexes[$row['value_index']])) {
                    $delete[] = $row['value_id'];
                    continue;
                }
            }
            foreach ($newValues as $value) {
                $valueObject = new Varien_Object($value);
                $key = join('-', array($websiteId, $value['value_index']));

                $pricingValue = $valueObject->getPricingValue();
                if ($pricingValue == '' || is_null($pricingValue)) {
                    $pricingValue = null;
                }
                else {
                    $pricingValue = Mage::app()->getLocale()->getNumber($pricingValue);
                }

                // update
                if (isset($oldValues[$key])) {
                    $oldValue = $oldValues[$key];

                    if ($websiteId && $valueObject->getUseDefaultValue()) {
                        $delete[] = $oldValue['value_id'];
                    }
                    else {
                        $update[$oldValue['value_id']] = array(
                            'pricing_value' => $pricingValue,
                            'is_percent'    => intval($valueObject->getIsPercent())
                        );
                    }
                }
                // insert
                else {
                    if ($websiteId && $valueObject->getUseDefaultValue()) {
                        continue;
                    }
                    $insert[] = array(
                        'product_super_attribute_id'    => $attribute->getId(),
                        'value_index'                   => $valueObject->getValueIndex(),
                        'is_percent'                    => intval($valueObject->getIsPercent()),
                        'pricing_value'                 => $pricingValue,
                        'website_id'                    => $websiteId
                    );
                }
                $key = join('-', array(0, $value['value_index']));
                if (!isset($oldValues[$key])) {
                    $insert[] = array(
                        'product_super_attribute_id'    => $attribute->getId(),
                        'value_index'                   => $valueObject->getValueIndex(),
                        'is_percent'                    => 0,
                        'pricing_value'                 => null,
                        'website_id'                    => 0
                    );
                }
            }
        }

        if (!empty($delete)) {
            $where = $this->_getWriteAdapter()->quoteInto('value_id IN(?)', $delete);
            $this->_getWriteAdapter()->delete($this->_priceTable, $where);
        }

        if (!empty($update)) {
            foreach ($update as $valueId => $valueData) {
                $where = $this->_getWriteAdapter()->quoteInto('value_id=?', $valueId);
                $this->_getWriteAdapter()->update($this->_priceTable, $valueData, $where);
            }
        }

        if (!empty($insert)) {
            foreach ($insert as $valueData) {
                $this->_getWriteAdapter()->insert($this->_priceTable, $valueData);
            }
        }

        return $this;
    }
}
