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

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId, 'custom_layout_update', 'backend_model', 'catalog/attribute_backend_customlayoutupdate');

$entityTypeId = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute($entityTypeId, 'custom_layout_update', 'backend_model', 'catalog/attribute_backend_customlayoutupdate');
