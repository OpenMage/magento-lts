<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogRule
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('catalogrule_product_price'), 'latest_start_date', 'date');
$installer->getConnection()->addColumn($this->getTable('catalogrule_product_price'), 'earliest_end_date', 'date');

$installer->endSetup();
