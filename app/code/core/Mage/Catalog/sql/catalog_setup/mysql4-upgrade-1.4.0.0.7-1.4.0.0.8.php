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

$installer->updateAttribute('catalog_category', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_category', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'url_key', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
$installer->updateAttribute('catalog_product', 'url_path', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
