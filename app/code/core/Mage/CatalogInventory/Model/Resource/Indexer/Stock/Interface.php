<?php
/**
 * CatalogInventory Stock Indexer Interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogInventory
 */
interface Mage_CatalogInventory_Model_Resource_Indexer_Stock_Interface
{
    /**
     * Reindex all stock status data
     *
     */
    public function reindexAll();

    /**
     * Reindex stock status data for defined ids
     *
     * @param int|array $entityIds
     */
    public function reindexEntity($entityIds);

    /**
     * Set Product Type Id for indexer
     *
     * @param string $typeId
     */
    public function setTypeId($typeId);

    /**
     * Retrieve Product Type Id for indexer
     *
     * @throws Mage_Core_Exception
     *
     */
    public function getTypeId();
}
