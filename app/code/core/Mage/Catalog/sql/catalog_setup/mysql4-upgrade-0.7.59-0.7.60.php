<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_x', 'smallint unsigned not null after `file_extension`');
$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_y', 'smallint unsigned not null after `image_size_x`');

$installer->endSetup();
