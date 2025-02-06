<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Index
 */

/** @var Mage_Index_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn(
    $this->getTable('index/process'),
    'mode',
    "enum('real_time','manual') DEFAULT 'real_time' NOT NULL after `ended_at`",
);
