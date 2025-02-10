<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'tax_class_id', 'apply_to', 'simple,configurable,virtual');

$installer->endSetup();
