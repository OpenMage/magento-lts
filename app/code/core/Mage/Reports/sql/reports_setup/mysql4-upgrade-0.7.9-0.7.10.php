<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->modifyColumn(
    $installer->getTable('reports/compared_product_index'),
    'visitor_id',
    'INT(10) UNSIGNED NULL',
);

$installer->endSetup();
