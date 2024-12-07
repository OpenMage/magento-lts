<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$installer->getTable('core_url_rewrite')}`
    DROP `entity_id`,
    DROP `type`,
    ADD `is_system` tinyint(1) unsigned default '1' AFTER `target_path`,
    DROP INDEX `store_id`,
    ADD INDEX `FK_CORE_URL_REWRITE_STORE` (`store_id`),
    DROP INDEX `id_path`,
    ADD UNIQUE `UNQ_PATH` (`store_id`, `id_path`, `is_system`),
    DROP INDEX `request_path`,
    ADD UNIQUE `UNQ_REQUEST_PATH` (`store_id`, `request_path`),
    DROP INDEX `target_path`,
    ADD INDEX `IDX_TARGET_PATH` (`store_id`, `target_path`);
DROP TABLE IF EXISTS `{$installer->getTable('core_url_rewrite_tag')}`;
");

$installer->endSetup();
