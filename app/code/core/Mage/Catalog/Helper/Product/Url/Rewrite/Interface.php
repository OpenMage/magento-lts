<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Product url rewrite interface
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Helper_Product_Url_Rewrite_Interface
{
    /**
     * Prepare and return select
     *
     * @param int $categoryId
     * @param int $storeId
     * @return Varien_Db_Select
     */
    public function getTableSelect(array $productIds, $categoryId, $storeId);

    /**
     * Prepare url rewrite left join statement for given select instance and store_id parameter.
     *
     * @param int $storeId
     * @return Mage_Catalog_Helper_Product_Url_Rewrite_Interface
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId);
}
