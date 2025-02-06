<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;

$applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array(Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE, $applyTo)) {
    $applyTo[] = Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'group_price', 'apply_to', implode(',', $applyTo));
}
