<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$this->getConnection()->dropColumn($this->getTable('customer_address_entity'), 'store_id');

$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    ADD COLUMN `website_id` SMALLINT(5) UNSIGNED AFTER `attribute_set_id`,
    ADD COLUMN `email` VARCHAR(255) NOT NULL AFTER `website_id`,
    ADD COLUMN `group_id` SMALLINT(3) UNSIGNED NOT NULL AFTER `email`,
    ADD INDEX IDX_AUTH(`email`, `website_id`),
    ADD CONSTRAINT `FK_CUSTOMER_WEBSITE` FOREIGN KEY `FK_CUSTOMER_WEBSITE` (`website_id`)
        REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE SET NULL ON UPDATE CASCADE;
");

$emailAttributeId = $installer->getAttributeId('customer', 'email');
$groupAttributeId = $installer->getAttributeId('customer', 'group_id');

$installer->run("
    UPDATE {$this->getTable('customer_entity')} customer, {$this->getTable('core_store')} store
    SET customer.website_id=store.website_id
    WHERE store.store_id=customer.store_id;

    UPDATE {$this->getTable('core_website')} SET code='admin', name='Admin' WHERE website_id=0;

    UPDATE {$this->getTable('customer_entity')} customer, {$this->getTable('customer_entity_varchar')} varchar_attribute
    SET customer.email=varchar_attribute.value
    WHERE varchar_attribute.entity_id=customer.entity_id
    AND varchar_attribute.attribute_id={$emailAttributeId};

    UPDATE {$this->getTable('customer_entity')} customer, {$this->getTable('customer_entity_int')} int_attribute
    SET customer.group_id=int_attribute.value
    WHERE int_attribute.entity_id=customer.entity_id
    AND int_attribute.attribute_id={$groupAttributeId};
");

$installer->getConnection()->dropColumn($this->getTable('customer_entity'), 'parent_id');
$installer->installEntities();
$installer->endSetup();
