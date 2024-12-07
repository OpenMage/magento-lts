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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup()->run("

ALTER TABLE {$this->getTable('catalog_product_entity_tier_price')}
    ADD COLUMN `customer_group_id` smallint(5) unsigned NOT NULL default '0' AFTER `entity_id`,
    ADD CONSTRAINT `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_GROUP` FOREIGN KEY (`customer_group_id`)
    REFERENCES {$this->getTable('customer_group')} (`customer_group_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;

update {$this->getTable('catalog_product_entity_tier_price')} set `customer_group_id`=(select `customer_group_id` from {$this->getTable('customer_group')} limit 1);

")->endSetup();
