<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Index_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->getConnection()->changeColumn(
    $this->getTable('index/process'),
    'status',
    'status',
    "enum('pending','working','require_reindex') DEFAULT 'pending' NOT NULL",
);
