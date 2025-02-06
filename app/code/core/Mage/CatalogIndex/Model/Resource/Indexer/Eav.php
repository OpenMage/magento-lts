<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */

/**
 * Eav indexer resource model
 *
 * @category   Mage
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
