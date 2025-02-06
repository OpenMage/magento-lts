<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_wysiwyg_enabled',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
);

$installer->endSetup();
