<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'category_ids');
$installer->getConnection()->dropColumn($installer->getTable('catalog/product'), 'category_ids');

$installer->endSetup();
