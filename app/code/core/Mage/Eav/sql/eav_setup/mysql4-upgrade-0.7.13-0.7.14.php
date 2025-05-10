<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('eav/entity_type'), 'additional_attribute_table', 'varchar(255) NOT NULL DEFAULT \'\'');
$installer->getConnection()->addColumn($installer->getTable('eav/entity_type'), 'entity_attribute_collection', 'varchar(255) NOT NULL DEFAULT \'\'');
$installer->run("
    CREATE TABLE `{$installer->getTable('eav/attribute_label')}` (
        `attribute_label_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
        `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
        `value` varchar(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`attribute_label_id`),
        KEY `IDX_ATTRIBUTE_LABEL_ATTRIBUTE` (`attribute_id`),
        KEY `IDX_ATTRIBUTE_LABEL_STORE` (`store_id`),
        KEY `IDX_ATTRIBUTE_LABEL_ATTRIBUTE_STORE` (`attribute_id`, `store_id`),
        CONSTRAINT `FK_ATTRIBUTE_LABEL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_ATTRIBUTE_LABEL_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
