<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup  $installer */
$installer = $this;

$installer->removeAttribute('catalog_category', 'custom_design_apply');

// the fix for a typo that was in mysql4-upgrade-0.7.71-0.7.72 line 32
$installer->removeAttribute('catalog_product', 'category_ids');
