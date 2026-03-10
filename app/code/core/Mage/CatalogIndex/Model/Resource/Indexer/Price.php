<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Price indexer resource model
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Resource_Indexer_Price extends Mage_CatalogIndex_Model_Resource_Indexer_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('catalogindex/price', 'index_id');

        $this->_entityIdFieldName       = 'entity_id';
        $this->_attributeIdFieldName    = 'attribute_id';
        $this->_storeIdFieldName        = 'store_id';
    }
}
