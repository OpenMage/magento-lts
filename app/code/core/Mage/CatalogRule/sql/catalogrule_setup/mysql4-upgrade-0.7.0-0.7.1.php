<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('catalogrule'), 'simple_action', 'varchar(32) not null');
$installer->getConnection()->addColumn($this->getTable('catalogrule'), 'discount_amount', 'decimal(12,4) not null');

$installer->endSetup();
