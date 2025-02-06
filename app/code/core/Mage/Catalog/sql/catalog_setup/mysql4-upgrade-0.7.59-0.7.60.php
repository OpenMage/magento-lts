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
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_x', 'smallint unsigned not null after `file_extension`');
$installer->getConnection()->addColumn($installer->getTable('catalog/product_option'), 'image_size_y', 'smallint unsigned not null after `image_size_x`');

$installer->endSetup();
