<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
ALTER TABLE `{$installer->getTable('core/url_rewrite')}`
  DROP INDEX `UNQ_PATH`,
  DROP INDEX `UNQ_REQUEST_PATH`,
  DROP INDEX `IDX_TARGET_PATH`,
  ADD UNIQUE `UNQ_PATH` (`id_path`, `is_system`, `store_id`),
  ADD UNIQUE `UNQ_REQUEST_PATH` (`request_path`, `store_id`),
  ADD INDEX `IDX_TARGET_PATH` (`target_path`, `store_id`);
");

$installer->endSetup();
