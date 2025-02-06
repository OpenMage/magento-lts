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

$installer->updateEntityType('catalog_category', 'entity_attribute_collection', 'catalog/category_attribute_collection');
$installer->updateEntityType('catalog_product', 'entity_attribute_collection', 'catalog/product_attribute_collection');

$installer->endSetup();
