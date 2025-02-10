<?php
/**
 * Eav indexer resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Eav extends Mage_CatalogIndex_Model_Resource_Indexer_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogindex/eav', 'index_id');

        $this->_entityIdFieldName       = 'entity_id';
        $this->_attributeIdFieldName    = 'attribute_id';
        $this->_storeIdFieldName        = 'store_id';
    }
}
