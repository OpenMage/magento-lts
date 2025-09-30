<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$data = [
    [
        'type_id'     => 1,
        'type_code'   => 'hour',
        'period'      => 1,
        'period_type' => 'HOUR',
    ],

    [
        'type_id'     => 2,
        'type_code'   => 'day',
        'period'      => 1,
        'period_type' => 'DAY',
    ],
];

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('log/summary_type_table'), $bind);
}
