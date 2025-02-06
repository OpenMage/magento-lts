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

$fieldList = ['price','special_price','special_from_date','special_to_date',
    'minimal_price','cost','tier_price'];
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('virtual', $applyTo)) {
        $applyTo[] = 'virtual';
        $installer->updateAttribute('catalog_product', $field, 'apply_to', implode(',', $applyTo));
    }
}

$installer->endSetup();
