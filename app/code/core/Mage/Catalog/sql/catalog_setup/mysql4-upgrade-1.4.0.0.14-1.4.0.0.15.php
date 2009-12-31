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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();
$installer->getConnection()->addColumn(
    $installer->getTable('catalog/eav_attribute'),
    'is_wysiwyg_enabled',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");

$categoryEntityTypeId = $installer->getEntityTypeId('catalog_category');
$productEntityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($categoryEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'description', 'is_html_allowed_on_front', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute($productEntityTypeId, 'short_description', 'is_html_allowed_on_front', 1);

$installer->endSetup();
