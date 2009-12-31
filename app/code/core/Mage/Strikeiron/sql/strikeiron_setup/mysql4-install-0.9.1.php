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
 * @package     Mage_Strikeiron
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
/*Table structure for table `strikeiron_tax_rate` */

DROP TABLE IF EXISTS {$this->getTable('strikeiron_tax_rate')};

CREATE TABLE {$this->getTable('strikeiron_tax_rate')} (
  `tax_rate_id` tinyint(4) NOT NULL auto_increment,
  `tax_country_id` varchar(6) default NULL,
  `tax_region_id` mediumint(9) unsigned default NULL,
  `tax_postcode` varchar(12) default NULL,
  `rate_value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Strikeiron tax rates';

   ");

$installer->endSetup();
