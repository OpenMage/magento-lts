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

$productEntityTypeId = $installer->getEntityTypeId('catalog_product');

$installer->updateAttribute($productEntityTypeId, 'minimal_price', 'is_required', 0);
$installer->updateAttribute($productEntityTypeId, 'required_options', 'is_required', 0);
$installer->updateAttribute($productEntityTypeId, 'has_options', 'is_required', 0);

$installer->endSetup();
