<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Sales_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('sales/quote'),
    'remote_ip',
    'remote_ip',
    'VARCHAR(255) default NULL COMMENT \'Remote Ip\'',
);

$installer->endSetup();
