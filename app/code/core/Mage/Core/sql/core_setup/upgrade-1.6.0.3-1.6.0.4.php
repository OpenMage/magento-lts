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
    $this->getTable('core_config_data'),
    $connection->prepareSqlCondition('path', [
        'like' => 'google/checkout%',
    ]),
);

$installer->endSetup();
