<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Product Type Price Indexer interface
 *
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Model_Resource_Product_Indexer_Price_Interface
{
    /**
     * Reindex temporary (price result data) for all products
     *
     */
    public function reindexAll();

    /**
     * Reindex temporary (price result data) for defined product(s)
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds);

    /**
     * Register data required by product type process in event object
     */
    public function registerEvent(Mage_Index_Model_Event $event);
}
