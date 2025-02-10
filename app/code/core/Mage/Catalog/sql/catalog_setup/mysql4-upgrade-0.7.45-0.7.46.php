<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('catalog_product', 'tier_price', 'is_used_for_price_rules', '0');

$installer->endSetup();
