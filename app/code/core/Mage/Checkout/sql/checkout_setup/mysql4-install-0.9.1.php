<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Chronopay
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
CREATE TABLE `{$installer->getTable('checkout_agreement')}` (
   `agreement_id` int(10) unsigned NOT NULL auto_increment,
   `name` varchar(255) NOT NULL default '',
   `content` text NOT NULL,
   `checkbox_text` text NOT NULL,
   `is_active` tinyint(4) NOT NULL default '0',
    PRIMARY KEY  (`agreement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
CREATE TABLE `{$this->getTable('checkout_agreement_store')}` (
    `agreement_id` int(10) unsigned not null,
    `store_id` smallint(5) unsigned not null,
    UNIQUE KEY (`agreement_id`, `store_id`),
    CONSTRAINT `FK_CHECKOUT_AGREEMENT` FOREIGN KEY (`agreement_id`) REFERENCES `{$installer->getTable('checkout_agreement')}` (`agreement_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_CHECKOUT_AGREEMENT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup();
