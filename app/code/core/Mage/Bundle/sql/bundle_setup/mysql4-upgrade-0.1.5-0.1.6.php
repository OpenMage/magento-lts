<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();
$installer->updateAttribute('catalog_product', 'price_view', 'used_in_product_listing', 1);
$installer->updateAttribute('catalog_product', 'shipment_type', 'used_in_product_listing', 1);
$installer->updateAttribute('catalog_product', 'weight_type', 'used_in_product_listing', 1);
$installer->endSetup();
