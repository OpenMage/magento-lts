<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('catalog/product_website')} ROW_FORMAT=DYNAMIC;");
$installer->run("ALTER TABLE {$this->getTable('catalog/product_relation')} ROW_FORMAT=DYNAMIC;");

$installer->endSetup();
