<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('catalogindex_price')}
    ADD COLUMN `tax_class_id` smallint(6) NOT NULL DEFAULT 0;
");

$installer->run("
    ALTER TABLE {$installer->getTable('catalogindex_minimal_price')}
    ADD COLUMN `tax_class_id` smallint(6) NOT NULL DEFAULT 0;
");

$installer->endSetup();
