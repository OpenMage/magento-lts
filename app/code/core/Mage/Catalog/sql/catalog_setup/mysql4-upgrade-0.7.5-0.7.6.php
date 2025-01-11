<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();

$conn->addColumn($this->getTable('catalog_product_entity'), 'category_ids', 'text after `sku`');

$installer->run("update `{$this->getTable('catalog_product_entity')}` set `category_ids`=(select group_concat(`category_id` separator ',') from `{$this->getTable('catalog_category_product')}` where `product_id`=`entity_id`)");

$installer->endSetup();
