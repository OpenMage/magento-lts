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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->addAttribute('catalog_product', 'samples_title', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Samples title',
        'input'             => '',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => false,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'downloadable',
        'is_configurable'   => false
    ));

$installer->addAttribute('catalog_product', 'links_title', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Links title',
        'input'             => '',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => false,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => 'downloadable',
        'is_configurable'   => false
    ));

$installer->endSetup();
