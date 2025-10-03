<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $this */
$this->startSetup();

$table = $this->getTable('catalog/category');
$tableTmp = $table . '_tmp';
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
