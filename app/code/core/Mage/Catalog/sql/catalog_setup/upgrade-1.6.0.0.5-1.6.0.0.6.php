<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
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
