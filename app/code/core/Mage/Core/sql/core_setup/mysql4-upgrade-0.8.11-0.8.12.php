<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('core_store'),
    'name',
    'name',
    'varchar(255) not null',
    true,
);

$installer->getConnection()->changeColumn(
    $installer->getTable('core_store_group'),
    'name',
    'name',
    'varchar(255) not null',
    true,
);

$installer->endSetup();
