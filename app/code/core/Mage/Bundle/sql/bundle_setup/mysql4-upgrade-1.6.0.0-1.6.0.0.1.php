<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

$installFile = __DIR__ . DS . 'upgrade-1.6.0.0-1.6.0.0.1.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$memoryTables = [
    'bundle/option_indexer_tmp',
    'bundle/selection_indexer_tmp',
    'bundle/price_indexer_tmp',
];

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($installer->getTable($table), Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY);
}
