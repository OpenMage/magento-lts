<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer  = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $this->getTable('salesrule'),
    'conditions_serialized',
    'conditions_serialized',
    'mediumtext CHARACTER SET utf8 NOT NULL',
);
$installer->getConnection()->changeColumn(
    $this->getTable('salesrule'),
    'actions_serialized',
    'actions_serialized',
    'mediumtext CHARACTER SET utf8 NOT NULL',
);

$installer->endSetup();
