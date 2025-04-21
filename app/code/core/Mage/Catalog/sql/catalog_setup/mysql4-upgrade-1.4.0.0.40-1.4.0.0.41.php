<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('catalog/product_super_attribute_pricing')}
    CHANGE `pricing_value` `pricing_value` DECIMAL(20,4) NULL");

$installer->endSetup();
