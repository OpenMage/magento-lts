<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('tag_summary'),
    'base_popularity',
    'int(11) UNSIGNED DEFAULT \'0\' NOT NULL AFTER `popularity`',
);

$installer->getConnection()->changeColumn(
    $this->getTable('tag_relation'),
    'customer_id',
    'customer_id',
    'INT(10) UNSIGNED NULL DEFAULT NULL',
);

$installer->endSetup();
