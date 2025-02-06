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

// status attribute
$installer->updateAttribute('catalog_product', 'status', 'is_visible_in_advanced_search', 0);
// visibility attribute
$installer->updateAttribute('catalog_product', 'visibility', 'is_visible_in_advanced_search', 0);

$installer->endSetup();
