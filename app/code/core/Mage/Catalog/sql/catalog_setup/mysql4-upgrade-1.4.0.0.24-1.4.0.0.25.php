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

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_used_for_price_rules',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'deprecated after 1.4.0.1'"
);

$installer->getConnection()->addColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_used_for_promo_rules',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'"
);

$installer->run("UPDATE {$installer->getTable('catalog/eav_attribute')}
    SET is_used_for_promo_rules = is_used_for_price_rules");
