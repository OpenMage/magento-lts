<?php

/**
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
