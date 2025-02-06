<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

$this->run("

update {$this->getTable('eav_entity_attribute')} set `sort_order`=10 where `attribute_id`=(select `attribute_id` from {$this->getTable('eav_attribute')} where `attribute_code`='tier_price');

alter table {$this->getTable('catalog_product_entity_tier_price')} add column `all_groups` tinyint (1)UNSIGNED  DEFAULT '1' NOT NULL  after `entity_id`;

");
