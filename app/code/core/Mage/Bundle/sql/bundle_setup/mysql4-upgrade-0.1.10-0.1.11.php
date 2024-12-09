<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('bundle/stock_index')}` (
  `entity_id` INT(10) UNSIGNED NOT NULL,
  `website_id` SMALLINT(5) UNSIGNED NOT NULL,
  `stock_id` SMALLINT(5) UNSIGNED NOT NULL,
  `option_id` INT(10) UNSIGNED DEFAULT 0,
  `stock_status` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`entity_id`,`stock_id`,`website_id`,`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
