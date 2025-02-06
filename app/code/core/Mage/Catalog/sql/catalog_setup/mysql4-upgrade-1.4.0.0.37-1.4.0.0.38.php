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

$productEntityTypeId = $installer->getEntityTypeId('catalog_category');
$installer->updateAttribute($productEntityTypeId, 'include_in_menu', 'is_required', true);
