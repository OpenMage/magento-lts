<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("

-- DROP TABLE if exists {$this->getTable('paygate_authorizenet_debug')};
CREATE TABLE {$this->getTable('paygate_authorizenet_debug')} (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `request_body` text,
  `response_body` text,
  `request_serialized` text,
  `result_serialized` text,
  `request_dump` text,
  `result_dump` text,
  PRIMARY KEY  (`debug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
