<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
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
