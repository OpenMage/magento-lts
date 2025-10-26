<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$connection = $installer->getConnection();

$tableName = $installer->getTable('catalogsearch/search_query');
$indexNameToCreate = $installer->getIdxName($tableName, ['synonym_for']);
$connection->addIndex($tableName, $indexNameToCreate, ['synonym_for']);
