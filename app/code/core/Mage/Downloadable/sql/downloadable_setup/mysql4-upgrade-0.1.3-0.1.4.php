<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('downloadable/sample'), 'sample_url', "varchar(255) NOT NULL default '' AFTER `product_id`");
$installer->endSetup();
