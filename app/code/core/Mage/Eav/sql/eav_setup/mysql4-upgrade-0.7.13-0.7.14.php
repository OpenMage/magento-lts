<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
