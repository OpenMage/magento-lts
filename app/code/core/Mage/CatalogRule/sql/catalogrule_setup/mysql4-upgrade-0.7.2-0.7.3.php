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

$installer->getConnection()->addColumn($this->getTable('catalogrule_product_price'), 'latest_start_date', 'date');
$installer->getConnection()->addColumn($this->getTable('catalogrule_product_price'), 'earliest_end_date', 'date');

$installer->endSetup();
