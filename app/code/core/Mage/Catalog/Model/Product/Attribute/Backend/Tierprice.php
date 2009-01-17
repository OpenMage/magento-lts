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
 * Catalog product tier price backend attribute model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Tierprice extends Mage_Catalog_Model_Product_Attribute_Backend_Price
{
    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Backend_Tierprice
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('catalog/product_attribute_backend_tierprice');
    }

    /**
     * Validate data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  this
     */
    public function validate($object)
    {
        $tiers = $object->getData($this->getAttribute()->getName());
        if (empty($tiers)) {
            return $this;
        }
        $dup = array();
        foreach ($tiers as $tier) {
            if (!empty($tier['delete'])) {
                continue;
            }
            $key1 = implode('-', array($tier['website_id'], $tier['cust_group'], $tier['price_qty']));
            $key2 = implode('-', array(0, $tier['cust_group'], $tier['price_qty']));
            if (!empty($dup[$key1]) || !empty($dup[$key2])) {
                Mage::throwException(
                    Mage::helper('catalog')->__('Duplicate website tier price customer group and quantity.')
                );
            }
            $dup[$key1] = 1;
        }
        return $this;
    }

    /**
     * Assign tier prices to product data
     *
     * @param   Mage_Catalog_Model_Product $object
     * @return  Mage_Catalog_Model_Product_Attribute_Backend_Tierprice
     */
    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadProductPrices($object, $this->getAttribute());

        foreach ($data as $i=>$row) {
            if (!empty($row['all_groups'])) {
                $data[$i]['cust_group'] = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }
            if ($data[$i]['website_id'] == 0) {
                $rate = Mage::app()->getStore()->getBaseCurrency()->getRate(Mage::app()->getBaseCurrencyCode());
                if ($rate) {
                    $data[$i]['website_price'] = $data[$i]['price']/$rate;
                }
                else {
                    /**
                     * Remove tier price if rate not available
                     */
                    unset($data[$i]);
                }
            }
            else {
                $data[$i]['website_price'] = $data[$i]['price'];
            }

        }
        $object->setData($this->getAttribute()->getName(), $data);
        return $this;
    }

    public function afterSave($object)
    {
        $this->_getResource()->deleteProductPrices($object);
        $tierPrices = $object->getData($this->getAttribute()->getName());

        if (!is_array($tierPrices)) {
            return $this;
        }

        foreach ($tierPrices as $tierPrice) {
            if (empty($tierPrice['price_qty']) || !isset($tierPrice['price']) || !empty($tierPrice['delete'])) {
                continue;
            }

            $useForAllGroups = $tierPrice['cust_group'] == Mage_Customer_Model_Group::CUST_GROUP_ALL;

            $data = array();
            $data['website_id']        = $tierPrice['website_id'];
            $data['all_groups']        = $useForAllGroups;
            $data['customer_group_id'] = !$useForAllGroups ? $tierPrice['cust_group'] : 0;
            $data['qty']               = $tierPrice['price_qty'];
            $data['value']             = $tierPrice['price'];

            $this->_getResource()->insertProductPrice($object, $data);
        }

        return $this;
    }

    public function afterDelete($object)
    {
        $this->_getResource()->deleteProductPrices($object);
        return $this;
    }
}