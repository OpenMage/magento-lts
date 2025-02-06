<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
