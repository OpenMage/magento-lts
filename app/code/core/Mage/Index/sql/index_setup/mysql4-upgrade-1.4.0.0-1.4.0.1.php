<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/** @var Mage_Index_Model_Resource_Setup $installer */
$installer = $this;

$installer->getConnection()->addColumn(
    $this->getTable('index/process'),
    'mode',
    "enum('real_time','manual') DEFAULT 'real_time' NOT NULL after `ended_at`",
);
