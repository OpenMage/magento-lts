<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Eav_Model_Entity_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
foreach (['datetime', 'decimal', 'int', 'text', 'varchar'] as $type) {
    $tableName = $installer->getTable('eav_entity_' . $type);
    $conn->addKey($tableName, 'UNQ_ATTRIBUTE_VALUE', ['entity_id','attribute_id','store_id'], 'unique');
}

$installer->endSetup();
