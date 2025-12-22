<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * CatalogIndex Grouped Products Data Retriever Resource Model
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Data_Grouped extends Mage_CatalogIndex_Model_Resource_Data_Abstract
{
    /**
     * Return minimal prices for specified products
     *
     * @param  array $products
     * @param  array $priceAttributes
     * @param  int   $store
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
                    'tax_class_id'      => $taxClassId,
                ];
            }
        }

        return $result;
    }

    /**
     * Prepare select statement before 'fetchLinkInformation' function result fetch
     *
     * @param int    $store
     * @param string $table
     * @param string $idField
     * @param string $whereField
     * @param int    $id
     * @param array  $additionalWheres
     */
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = [])
    {
        $this->_addAttributeFilter($this->_getLinkSelect(), 'required_options', 'l', $idField, $store, 0);
    }
}
