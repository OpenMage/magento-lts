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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('catalog/product_entity')}` ADD `has_options` SMALLINT(1) NOT NULL DEFAULT '0';
");

$installer->addAttribute('catalog_product', 'has_options', array(
    'type' => 'static',
    'visible'=>false,
    'default' => false
));
$installer->run("
    UPDATE `{$installer->getTable('catalog/product_entity')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_option')}` GROUP BY product_id
    ));
    UPDATE `{$installer->getTable('catalog/product_entity')}` SET `has_options` = '1'
    WHERE (entity_id IN (
        SELECT product_id FROM `{$installer->getTable('catalog/product_super_attribute')}` GROUP BY product_id
    ));
");

$installer->endSetup();
