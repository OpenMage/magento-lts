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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();
$installer->run("
    ALTER TABLE `{$installer->getTable('catalog_product_entity')}`
        CHANGE `type_id` `type_id` VARCHAR(32) DEFAULT 'simple' NOT NULL;
    UPDATE `{$installer->getTable('catalog_product_entity')}`
        SET `type_id` = CASE `type_id`
            WHEN '1' THEN 'simple'
            WHEN '2' THEN 'bundle'
            WHEN '3' THEN 'configurable'
            WHEN '4' THEN 'grouped'
            WHEN '5' THEN 'virtual'
            ELSE `type_id` END;
");
$installer->endSetup();
