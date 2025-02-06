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

$installer->getConnection()->changeColumn(
    $this->getTable('index/process'),
    'status',
    'status',
    "enum('pending','working','require_reindex') DEFAULT 'pending' NOT NULL",
);
