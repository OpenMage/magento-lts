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
        $stores = Mage::getModel('core/store')->getCollection()->setLoadDefault(false)->load();
        $storeObject = $stores->getItemById($store);
        $website = $storeObject->getWebsiteId();
        $result = array();
        $fields = array('customer_group_id', 'minimal_value'=>'MIN(value)');
        $select = $this->_getReadAdapter()->select()
            ->from(array('base'=>$this->getTable('catalogindex/price')), array(
                'customer_group_id', 'minimal_value' => 'value', 'tax_class_id'
            ))
            ->where('base.entity_id in (?)', $products)
            ->where('base.attribute_id in (?)', $priceAttributes)
            ->where('base.website_id = ?', $website)
            ->order(array('customer_group_id', 'value'));
        $select = $this->_getReadAdapter()->select()
            ->from(array('blah' => new Zend_Db_Expr("({$select})")))
            ->group(new Zend_Db_Expr(1));
        $visible = $this->_getReadAdapter()->fetchAll($select);

        $groups = Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups();
        foreach ($groups as $group) {
            $resultMinimal    = null;
            $resultTaxClassId = 0;
            $taxClassId       = 0;
            $customerGroup = $group->getId();

            $typedProducts = Mage::getSingleton('catalogindex/retreiver')->assignProductTypes($products);
            foreach ($typedProducts as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                foreach ($typeIds as $id) {
                    $finalPrice = $retreiver->getFinalPrice($id, $storeObject, $group);
                    if ((null === $resultMinimal) || ($finalPrice < $resultMinimal)) {
                        $resultMinimal    = $finalPrice;
                        $resultTaxClassId = $retreiver->getTaxClassId($id, $storeObject);
                    }

                    $tiers = $retreiver->getTierPrices($id, $storeObject);
                    foreach ($tiers as $tier) {
                        if ($tier['customer_group_id'] != $customerGroup && !$tier['all_groups']) {
                            continue;
                        }
                        if ((null === $resultMinimal) || ($tier['value'] < $resultMinimal)) {
                            $resultMinimal    = $tier['value'];
                            $resultTaxClassId = $retreiver->getTaxClassId($tier['entity_id'], $storeObject);
                        }
                    }
                }
            }

            foreach ($visible as $one) {
                if ($one['customer_group_id'] != $customerGroup) {
                    continue;
                }

                if ((null === $resultMinimal) || ($one['minimal_value'] < $resultMinimal)) {
                    $resultMinimal = $one['minimal_value'];
                    $taxClassId    = $one['tax_class_id'];
                } else {
                    $taxClassId = $resultTaxClassId;
                }
            }

            if (!is_null($resultMinimal)){
                $result[] = array('customer_group_id'=>$customerGroup, 'minimal_value'=>$resultMinimal, 'tax_class_id' => $taxClassId);
            }
        }
        return $result;
    }
}