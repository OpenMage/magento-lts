<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'url_key',
    'frontend_label',
    'URL Key',
);

$installer->updateAttribute(
    Mage_Catalog_Model_Category::ENTITY,
    'url_key',
    'frontend_label',
    'URL Key',
);

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'options_container',
    'frontend_label',
    'Display Product Options In',
);
