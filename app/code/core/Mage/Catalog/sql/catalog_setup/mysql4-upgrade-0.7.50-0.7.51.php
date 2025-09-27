<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $this */
$this->startSetup();

$this->getConnection()->addColumn($this->getTable('catalog_category_entity'), 'children_count', 'INT NOT NULL');

$sql    = "SELECT * FROM `{$this->getTable('catalog_category_entity')}`";
$data   = $this->getConnection()->fetchAll($sql);

foreach ($data as $row) {
    $sql    = "SELECT COUNT(*) FROM `{$this->getTable('catalog_category_entity')}` WHERE `path` REGEXP '^{$row['path']}\/([0-9]+)$'";
    $count  = (int) $this->getConnection()->fetchOne($sql);

    $this->run("UPDATE `{$this->getTable('catalog_category_entity')}`
        SET `children_count` = $count
        WHERE `entity_id` = {$row['entity_id']}");
}

$this->endSetup();
