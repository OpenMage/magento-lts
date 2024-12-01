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
ALTER TABLE `{$installer->getTable('core/url_rewrite')}`
  DROP INDEX `UNQ_PATH`,
  DROP INDEX `UNQ_REQUEST_PATH`,
  DROP INDEX `IDX_TARGET_PATH`,
  ADD UNIQUE `UNQ_PATH` (`id_path`, `is_system`, `store_id`),
  ADD UNIQUE `UNQ_REQUEST_PATH` (`request_path`, `store_id`),
  ADD INDEX `IDX_TARGET_PATH` (`target_path`, `store_id`);
");

$installer->endSetup();
