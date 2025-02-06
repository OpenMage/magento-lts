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

$installer->updateAttribute('catalog_product', 'image_label', 'is_searchable', '0');
$installer->updateAttribute('catalog_product', 'small_image_label', 'is_searchable', '0');
$installer->updateAttribute('catalog_product', 'thumbnail_label', 'is_searchable', '0');

$installer->endSetup();
