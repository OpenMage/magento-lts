<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$connection = $installer->getConnection();

$tableName = $installer->getTable('catalogsearch/search_query');
$indexNameToCreate = $installer->getIdxName($tableName, ['synonym_for']);
$connection->addIndex($tableName, $indexNameToCreate, ['synonym_for']);
