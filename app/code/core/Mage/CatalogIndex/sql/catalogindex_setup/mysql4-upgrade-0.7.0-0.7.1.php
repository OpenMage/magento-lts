<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropForeignKey($installer->getTable('catalogindex_price'), 'FK_CATALOGINDEX_PRICE_CUSTOMER_GROUP');

$installer->endSetup();
