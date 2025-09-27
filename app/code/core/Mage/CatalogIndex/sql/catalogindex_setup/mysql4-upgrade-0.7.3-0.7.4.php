<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
TRUNCATE {$installer->getTable('catalogindex_eav')};
TRUNCATE {$installer->getTable('catalogindex_price')};
TRUNCATE {$installer->getTable('catalogindex_minimal_price')};
");

$installer->endSetup();
