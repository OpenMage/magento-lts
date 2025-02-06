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

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_used_for_price_rules',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'deprecated after 1.4.0.1'",
);

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_used_for_promo_rules',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'",
);

$installer->run("UPDATE {$installer->getTable('catalog/eav_attribute')}
    SET is_used_for_promo_rules = is_used_for_price_rules");
