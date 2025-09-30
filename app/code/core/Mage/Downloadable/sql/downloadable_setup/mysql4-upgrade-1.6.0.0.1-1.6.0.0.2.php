<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

$installFile = __DIR__ . DS . 'upgrade-1.6.0.0.1-1.6.0.0.2.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

/** @var Varien_Db_Adapter_Pdo_Mysql $connection */
$connection = $installer->getConnection();

$connection->changeTableEngine(
    $installer->getTable('downloadable/product_price_indexer_tmp'),
    Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY,
);
