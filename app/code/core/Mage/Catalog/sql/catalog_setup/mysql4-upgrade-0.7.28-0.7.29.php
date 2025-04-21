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

// status attribute
$installer->updateAttribute('catalog_product', 'status', 'is_visible_in_advanced_search', 0);
// visibility attribute
$installer->updateAttribute('catalog_product', 'visibility', 'is_visible_in_advanced_search', 0);

$installer->endSetup();
