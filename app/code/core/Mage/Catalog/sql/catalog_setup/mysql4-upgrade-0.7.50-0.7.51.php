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
