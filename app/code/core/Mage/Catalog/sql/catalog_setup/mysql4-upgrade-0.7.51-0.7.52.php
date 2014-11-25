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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $this Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$this->startSetup();
$table = $this->getTable('catalog/category');
$tableTmp = $table . "_tmp";
$this->run("DROP TABLE IF EXISTS `{$tableTmp}`");

$this->run("CREATE TABLE `{$tableTmp}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `children_count` int(11) NOT NULL,
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB;
");

$this->run("INSERT INTO {$tableTmp} (SELECT e.entity_id, COUNT( ee.entity_id ) as children_count
FROM `{$table}` e
INNER JOIN `{$table}` ee ON ee.path LIKE CONCAT( e.path, '/%' )
GROUP BY e.entity_id)");

$this->run("UPDATE {$table}, {$tableTmp}
SET {$table}.children_count = {$tableTmp}.children_count
WHERE {$table}.entity_id = {$tableTmp}.entity_id");

$this->run("DROP TABLE `{$tableTmp}`");

$this->endSetup();
