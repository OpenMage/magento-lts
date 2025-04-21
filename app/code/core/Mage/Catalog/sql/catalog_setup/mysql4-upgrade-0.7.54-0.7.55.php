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

$installer->updateAttribute('catalog_product', 'image_label', 'is_searchable', '0');
$installer->updateAttribute('catalog_product', 'small_image_label', 'is_searchable', '0');
$installer->updateAttribute('catalog_product', 'thumbnail_label', 'is_searchable', '0');

$installer->endSetup();
