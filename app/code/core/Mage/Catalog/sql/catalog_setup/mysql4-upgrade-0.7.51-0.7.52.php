<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
