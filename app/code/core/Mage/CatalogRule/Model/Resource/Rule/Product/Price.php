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
 * @package     Mage_CatalogRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Rule Product Aggregated Price per date Resource Model
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogRule_Model_Resource_Rule_Product_Price extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product_price', 'rule_product_price_id');
    }

    /**
     * Apply price rule price to price index table
     *
     * @param Varien_Db_Select $select
     * @param array|string $indexTable
     * @param string $entityId
     * @param string $customerGroupId
     * @param string $websiteId
     * @param array $updateFields       the array of fields for compare with rule price and update
     * @param string $websiteDate
     * @return $this
     */
    public function applyPriceRuleToIndexTable(
        Varien_Db_Select $select,
        $indexTable,
        $entityId,
        $customerGroupId,
        $websiteId,
        $updateFields,
        $websiteDate
    ) {
        if (empty($updateFields)) {
            return $this;
        }

        if (is_array($indexTable)) {
            foreach ($indexTable as $k => $v) {
                if (is_string($k)) {
                    $indexAlias = $k;
                } else {
                    $indexAlias = $v;
                }
                break;
            }
        } else {
            $indexAlias = $indexTable;
        }

        $select->join(array('rp' => $this->getMainTable()), "rp.rule_date = {$websiteDate}", array())
               ->where("rp.product_id = {$entityId} AND rp.website_id = {$websiteId} AND rp.customer_group_id = {$customerGroupId}");

        foreach ($updateFields as $priceField) {
            $priceCond = $this->_getWriteAdapter()->quoteIdentifier(array($indexAlias, $priceField));
            $priceExpr = $this->_getWriteAdapter()->getCheckSql("rp.rule_price < {$priceCond}", 'rp.rule_price', $priceCond);
            $select->columns(array($priceField => $priceExpr));
        }

        $query = $select->crossUpdateFromSelect($indexTable);
        $this->_getWriteAdapter()->query($query);

        return $this;
    }
}
