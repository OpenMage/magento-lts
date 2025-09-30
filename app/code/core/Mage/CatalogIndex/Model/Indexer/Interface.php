<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Catalog indexer interface
 *
 */
interface Mage_CatalogIndex_Model_Indexer_Interface
{
    /**
     * @return mixed
     */
    public function createIndexData(Mage_Catalog_Model_Product $object, ?Mage_Eav_Model_Entity_Attribute_Abstract $attribute = null);
}
