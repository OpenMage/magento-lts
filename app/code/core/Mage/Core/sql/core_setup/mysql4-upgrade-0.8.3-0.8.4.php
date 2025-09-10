<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
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
