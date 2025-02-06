<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Product_Condition_Interface
{
    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function applyToCollection($collection);

    /**
     * @param Magento_Db_Adapter_Pdo_Mysql $dbAdapter
     * @return string|Varien_Db_Select
     */
    public function getIdsSelect($dbAdapter);
}
