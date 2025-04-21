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

$productEntityTypeId = $installer->getEntityTypeId('catalog_product');

$installer->updateAttribute($productEntityTypeId, 'minimal_price', 'is_required', 0);
$installer->updateAttribute($productEntityTypeId, 'required_options', 'is_required', 0);
$installer->updateAttribute($productEntityTypeId, 'has_options', 'is_required', 0);

$installer->endSetup();
