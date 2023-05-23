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
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$connection = $installer->getConnection();
$connection->addColumn($installer->getTable('core_url_rewrite'), 'url_type', 'varchar(50) NULL AFTER `product_id`');
$installer->run("
UPDATE `{$installer->getTable('core_url_rewrite')}`
    SET `url_type`='category' 
    WHERE `id_path` LIKE 'category/%';
UPDATE `{$installer->getTable('core_url_rewrite')}`
    SET `url_type`='product' 
    WHERE `id_path` LIKE 'product/%';");
$connection->addIndex($installer->getTable('core_url_rewrite'), 'IDX_CORE_URL_REWRITE_URL_TYPE', array('url_type'));
$installer->endSetup();
