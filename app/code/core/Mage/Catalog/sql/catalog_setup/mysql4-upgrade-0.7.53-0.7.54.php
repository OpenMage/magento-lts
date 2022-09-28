<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_product', 'image_label', [
    'type'              => 'varchar',
    'label'             => 'Image Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->addAttribute('catalog_product', 'small_image_label', [
    'type'              => 'varchar',
    'label'             => 'Small Image Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->addAttribute('catalog_product', 'thumbnail_label', [
    'type'              => 'varchar',
    'label'             => 'Thumbnail Label',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => false,
    'searchable'        => true,
    'is_configurable'   => false,
]);

$installer->endSetup();
