<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$productEntityTypeId = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute($productEntityTypeId, 'include_in_menu', 'is_required', true);
