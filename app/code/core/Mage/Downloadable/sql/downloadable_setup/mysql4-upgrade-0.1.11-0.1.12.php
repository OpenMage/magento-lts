<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('downloadable/link_purchased_item'), 'product_id', "int(10) unsigned default '0' AFTER `order_item_id`");
$installer->endSetup();
