<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Core_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

$connection->delete(
    $this->getTable('eav/attribute'),
    $connection->prepareSqlCondition('attribute_code', 'enable_googlecheckout'),
);

$installer->endSetup();
