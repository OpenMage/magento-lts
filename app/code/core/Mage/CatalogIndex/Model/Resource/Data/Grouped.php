<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogIndex Grouped Products Data Retriever Resource Model
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Resource_Data_Grouped extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Return minimal prices for specified products
     *
     * @param array $products
     * @param array $priceAttributes
     * @param int $store
     * @return array
     */
    public function getMinimalPrice($products, $priceAttributes, $store)
    {
        $result = [];
        $store  = Mage::app()->getStore($store);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalogindex/price'), [
                'customer_group_id', 'value', 'tax_class_id'])
            ->where('entity_id IN(?)', $products)
            ->where('attribute_id IN(?)', $priceAttributes)
            ->where('website_id=?', $store->getWebsiteId());
        $prices = $select->query()->fetchAll();

        $groups = Mage::getSingleton('catalogindex/retreiver')->getCustomerGroups();
        foreach ($groups as $group) {
            $resultMinimal      = null;
            $resultTaxClassId   = 0;
            $taxClassId         = 0;
            $customerGroup      = $group->getId();

            $typedProducts = Mage::getSingleton('catalogindex/retreiver')
                ->assignProductTypes($products);
            foreach ($typedProducts as $type => $typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                foreach ($typeIds as $id) {
                    $finalPrice = $retreiver->getFinalPrice($id, $store, $group);
                    if (($resultMinimal === null) || ($finalPrice < $resultMinimal)) {
                        $resultMinimal    = $finalPrice;
                        $resultTaxClassId = $retreiver->getTaxClassId($id, $store);
                    }

                    $tiers = $retreiver->getTierPrices($id, $store);
                    foreach ($tiers as $tier) {
                        if ($tier['customer_group_id'] != $customerGroup && !$tier['all_groups']) {
                            continue;
                        }
                        if (($resultMinimal === null) || ($tier['value'] < $resultMinimal)) {
                            $resultMinimal    = $tier['value'];
                            $resultTaxClassId = $retreiver->getTaxClassId($tier['entity_id'], $store);
                        }
                    }
                }
            }

            foreach ($prices as $one) {
                if ($one['customer_group_id'] != $customerGroup) {
                    continue;
                }

                if (($resultMinimal === null) || ($one['value'] < $resultMinimal)) {
                    $resultMinimal = $one['value'];
                    $taxClassId    = $one['tax_class_id'];
                } else {
                    $taxClassId = $resultTaxClassId;
                }
            }

            if (!is_null($resultMinimal)) {
                $result[] = [
                    'customer_group_id' => $customerGroup,
                    'minimal_value'     => $resultMinimal,
                    'tax_class_id'      => $taxClassId
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare select statement before 'fetchLinkInformation' function result fetch
     *
     * @param int $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int $id
     * @param array $additionalWheres
     */
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = [])
    {
        $this->_addAttributeFilter($this->_getLinkSelect(), 'required_options', 'l', $idField, $store, 0);
    }
}
