<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();

$conn->addColumn($this->getTable('catalog_product_entity'), 'category_ids', 'text after `sku`');

$installer->run("update `{$this->getTable('catalog_product_entity')}` set `category_ids`=(select group_concat(`category_id` separator ',') from `{$this->getTable('catalog_category_product')}` where `product_id`=`entity_id`)");

$installer->endSetup();
