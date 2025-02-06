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

/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
$attribute = $installer->getAttribute('catalog_product', 'weight');

if ($attribute) {
    $installer->updateAttribute(
        $attribute['entity_type_id'],
        $attribute['attribute_id'],
        'frontend_input',
        $attribute['attribute_code'],
    );
}
