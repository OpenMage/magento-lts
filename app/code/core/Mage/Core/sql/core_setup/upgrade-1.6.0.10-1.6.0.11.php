<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Monolog\Level;

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();
$table = $installer->getTable('core/config_data');

$select = $connection->select()
    ->from($table)
    ->where('path = ?', Mage_Core_Helper_Data::XML_PATH_DEV_LOG_MAX_LEVEL);

$logConfig = $connection->fetchAll($select);

foreach ($logConfig as $config) {
    $zendValue = (int) $config['value'];
    $monologValue = match ($zendValue) {
        0 => Level::Emergency->value,
        1 => Level::Alert->value,
        2 => Level::Critical->value,
        3 => Level::Error->value,
        4 => Level::Warning->value,
        5 => Level::Notice->value,
        6 => Level::Info->value,
        default => Level::Debug->value,
    };

    $connection->insertOnDuplicate(
        $table,
        [
            'scope'     => $config['scope'],
            'scope_id'  => $config['scope_id'],
            'path'      => Mage_Core_Helper_Data::XML_PATH_DEV_LOG_MAX_LEVEL,
            'value'     => $monologValue,
        ],
        ['value'],
    );
}

$installer->endSetup();
