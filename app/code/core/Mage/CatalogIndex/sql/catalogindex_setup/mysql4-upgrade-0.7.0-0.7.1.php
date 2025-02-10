<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey($installer->getTable('catalogindex_price'), 'FK_CATALOGINDEX_PRICE_CUSTOMER_GROUP');

$installer->endSetup();
