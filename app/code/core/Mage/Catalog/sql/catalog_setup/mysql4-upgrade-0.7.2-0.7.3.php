<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

$this->run("

update {$this->getTable('eav_entity_attribute')} set `sort_order`=10 where `attribute_id`=(select `attribute_id` from {$this->getTable('eav_attribute')} where `attribute_code`='tier_price');

alter table {$this->getTable('catalog_product_entity_tier_price')} add column `all_groups` tinyint (1)UNSIGNED  DEFAULT '1' NOT NULL  after `entity_id`;

");
