<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
