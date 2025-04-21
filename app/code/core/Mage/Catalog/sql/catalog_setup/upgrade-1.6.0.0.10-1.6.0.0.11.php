<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer  = $this;

$attributeId = $this->getAttribute('catalog_product', 'group_price', 'attribute_id');
$installer->updateAttribute('catalog_product', $attributeId, [], null, 5);
