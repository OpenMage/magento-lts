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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogIndex Grouped Products Data Retriever Resource Model
 *
 * @category    Mage
 * @package     Mage_CatalogIndex
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $result = array();
        $store  = Mage::app()->getStore($store);

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('catalogindex/price'), array(
                'customer_group_id', 'value', 'tax_class_id'))
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
            foreach ($typedProducts as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('catalogindex/retreiver')->getRetreiver($type);
                foreach ($typeIds as $id) {
                    $finalPrice = $retreiver->getFinalPrice($id, $store, $group);
                    if ((null === $resultMinimal) || ($finalPrice < $resultMinimal)) {
                        $resultMinimal    = $finalPrice;
                        $resultTaxClassId = $retreiver->getTaxClassId($id, $store);
                    }

                    $tiers = $retreiver->getTierPrices($id, $store);
                    foreach ($tiers as $tier) {
                        if ($tier['customer_group_id'] != $customerGroup && !$tier['all_groups']) {
                            continue;
                        }
                        if ((null === $resultMinimal) || ($tier['value'] < $resultMinimal)) {
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

                if ((null === $resultMinimal) || ($one['value'] < $resultMinimal)) {
                    $resultMinimal = $one['value'];
                    $taxClassId    = $one['tax_class_id'];
                } else {
                    $taxClassId = $resultTaxClassId;
                }
            }

            if (!is_null($resultMinimal)){
                $result[] = array(
                    'customer_group_id' => $customerGroup,
                    'minimal_value'     => $resultMinimal,
                    'tax_class_id'      => $taxClassId
                );
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
    protected function _prepareLinkFetchSelect($store, $table, $idField, $whereField, $id, $additionalWheres = array())
    {
        $this->_addAttributeFilter($this->_getLinkSelect(), 'required_options', 'l', $idField, $store, 0);
    }
}
