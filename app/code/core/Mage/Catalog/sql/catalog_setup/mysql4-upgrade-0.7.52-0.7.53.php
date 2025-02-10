<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn($installer->getTable('catalog/product_option_title'), 'title', 'title', 'VARCHAR(255) NOT NULL default \'\'');
$installer->getConnection()->changeColumn($installer->getTable('catalog/product_option_type_title'), 'title', 'title', 'VARCHAR(255) NOT NULL default \'\'');

$installer->endSetup();
