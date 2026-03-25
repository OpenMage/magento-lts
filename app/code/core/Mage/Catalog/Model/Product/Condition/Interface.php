<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Product_Condition_Interface
{
    /**
     * @param  Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function applyToCollection($collection);

    /**
     * @param  Magento_Db_Adapter_Pdo_Mysql $dbAdapter
     * @return string|Varien_Db_Select
     */
    public function getIdsSelect($dbAdapter);
}
