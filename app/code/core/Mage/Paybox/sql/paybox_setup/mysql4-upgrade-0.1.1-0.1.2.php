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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paybox
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer = $this;

/* @var $installer Mage_Sales_Model_Entity_Setup */
$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('paybox/question_number')}`;
CREATE TABLE `{$this->getTable('paybox/question_number')}` (
  `account_id` int(10) unsigned NOT NULL auto_increment,
  `account_hash` varchar(50) NOT NULL default '',
  `increment_value` int(10) unsigned NOT NULL default 1,
  `reset_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
