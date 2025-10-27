<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
$installer = $this;

$attribute = 'special_price';
$installer
    ->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'special_price',
        'note',
        null,
    )
    ->updateAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'special_price',
        'frontend_class',
        'validate-special-price',
    )
;
