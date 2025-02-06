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
