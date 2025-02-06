<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
