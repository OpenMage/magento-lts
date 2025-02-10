<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup  $installer
 */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('downloadable/link'), 'link_url', "varchar(255) NOT NULL default '' AFTER `is_shareable`");
$installer->endSetup();
