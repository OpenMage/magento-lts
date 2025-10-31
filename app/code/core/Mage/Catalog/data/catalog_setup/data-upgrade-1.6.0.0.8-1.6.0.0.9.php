<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $this */
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
