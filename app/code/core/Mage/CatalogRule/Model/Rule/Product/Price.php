<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/**
 * Catalog Rule Product Aggregated Price per date Model
 *
 * @package    Mage_CatalogRule
 *
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price _getResource()
 * @method Mage_CatalogRule_Model_Resource_Rule_Product_Price getResource()
 * @method string getRuleDate()
 * @method $this setRuleDate(string $value)
 * @method int getCustomerGroupId()
 * @method $this setCustomerGroupId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method float getRulePrice()
 * @method $this setRulePrice(float $value)
 * @method int getWebsiteId()
 * @method $this setWebsiteId(int $value)
 * @method string getLatestStartDate()
 * @method $this setLatestStartDate(string $value)
 * @method string getEarliestEndDate()
 * @method $this setEarliestEndDate(string $value)
 */
class Mage_CatalogRule_Model_Rule_Product_Price extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product_price');
    }

    /**
     * Apply price rule price to price index table
     *
     * @param array|string $indexTable
     * @param string $entityId
     * @param string $customerGroupId
     * @param string $websiteId
     * @param array $updateFields       the array fields for compare with rule price and update
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
        $this->_getResource()->applyPriceRuleToIndexTable(
            $select,
            $indexTable,
            $entityId,
            $customerGroupId,
            $websiteId,
            $updateFields,
            $websiteDate,
        );

        return $this;
    }
}
