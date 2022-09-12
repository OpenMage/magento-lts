<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
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
