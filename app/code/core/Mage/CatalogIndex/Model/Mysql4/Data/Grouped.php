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
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_CatalogIndex_Model_Mysql4_Data_Grouped extends Mage_CatalogIndex_Model_Mysql4_Data_Abstract
{
    /**
     * Return minimal prices for specified products
     *
     * @param array $products
     * @param array $priceAttributes
     * @param int $store
     * @return mixed
     */
    public function getMinimalPrice($products, $priceAttributes, $store)
    {
        $result = array();
        $fields = array('customer_group_id', 'minimal_value'=>'MIN(value)');
        $select = $this->_getReadAdapter()->select()
            ->from(array('base'=>$this->getTable('catalogindex/price')), $fields)
            ->where('base.entity_id in (?)', $products)
            ->where('base.attribute_id in (?)', $priceAttributes)
            ->where('base.store_id = ?', $store)
            ->group('base.customer_group_id');
        $visible = $this->_getReadAdapter()->fetchAll($select);

        $groups = Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups();
        $stores = Mage::getModel('core/store')->getCollection()->setLoadDefault(false)->load();
        foreach ($groups as $group) {
            $resultMinimal = null;
            $customerGroup = $group->getId();
            $storeObject = $stores->getItemById($store);

            $typedProducts = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);
            foreach ($typedProducts as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                foreach ($typeIds as $id) {
                    $finalPrice = $retreiver->getFinalPrice($id, $storeObject, $group);

                    if (!is_null($resultMinimal)) {
                        $resultMinimal = min($resultMinimal, $finalPrice);
                    } else {
                        $resultMinimal = $finalPrice;
                    }

                    $tiers = $retreiver->getTierPrices($id, $storeObject);
                    foreach ($tiers as $tier) {
                        if ($tier['customer_group_id'] != $customerGroup && !$tier['all_groups']) {
                            continue;
                        }
                        if (!is_null($resultMinimal)) {
                            $resultMinimal = min($resultMinimal, $tier['value']);
                        } else {
                            $resultMinimal = $tier['value'];
                        }
                    }
                }
            }

            foreach ($visible as $one) {
                if ($one['customer_group_id'] != $customerGroup) {
                    continue;
                }

                if (is_null($resultMinimal)) {
                    $resultMinimal = $one['minimal_value'];
                } else {
                    $resultMinimal = min($one['minimal_value'], $resultMinimal);
                }
            }

            if (!is_null($resultMinimal)){
                $result[] = array('customer_group_id'=>$customerGroup, 'minimal_value'=>$resultMinimal);
            }
        }
        return $result;
    }
}