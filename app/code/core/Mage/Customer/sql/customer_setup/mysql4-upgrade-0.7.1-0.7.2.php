<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$this->getConnection()->dropColumn($this->getTable('customer_address_entity'), 'store_id');

$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_STORE`;
ALTER TABLE {$this->getTable('customer_entity')}
    CHANGE `store_id` `store_id` smallint(5) unsigned NULL DEFAULT '0';
ALTER TABLE {$this->getTable('customer_entity')}
    ADD CONSTRAINT `FK_CUSTOMER_ENTITY_STORE` FOREIGN KEY (`store_id`)
    REFERENCES {$this->getTable('core_store')} (`store_id`)
        ON UPDATE CASCADE
        ON DELETE SET NULL;
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_ENTITY_TYPE`,
    ADD INDEX `IDX_ENTITY_TYPE` (`entity_type_id`);
");
$installer->run("
ALTER TABLE {$this->getTable('customer_entity')}
    DROP INDEX `FK_CUSTOMER_ENTITY_PARENT_ENTITY`,
    ADD INDEX `IDX_PARENT_ENTITY` (`parent_id`);
");

$this->getConnection()->dropColumn($this->getTable('customer_entity_varchar'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_entity_text'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_entity_int'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_entity_decimal'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_entity_datetime'), 'store_id');

$this->getConnection()->dropColumn($this->getTable('customer_address_entity_varchar'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_address_entity_text'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_address_entity_int'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_address_entity_decimal'), 'store_id');
$this->getConnection()->dropColumn($this->getTable('customer_address_entity_datetime'), 'store_id');

$installer->endSetup();
