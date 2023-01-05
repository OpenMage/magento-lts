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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Customer_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('customer/eav_attribute_website')}` (
  `attribute_id` smallint(5) unsigned NOT NULL,
  `website_id` smallint(5) unsigned NOT NULL,
  `is_visible` tinyint(1) unsigned DEFAULT NULL,
  `is_required` tinyint(1) unsigned DEFAULT NULL,
  `default_value` text,
  `multiline_count` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`attribute_id`, `website_id`),
  KEY `IDX_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_ATTRIBUTE_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
    REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_EAV_ATTRIBUTE_WEBSITE_WEBSITE_CORE_WEBSITE` FOREIGN KEY (`website_id`)
    REFERENCES `{$installer->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
