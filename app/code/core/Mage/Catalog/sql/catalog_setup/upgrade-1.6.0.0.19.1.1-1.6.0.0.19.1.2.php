<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @var Mage_Catalog_Model_Resource_Setup $installer
 */
$installer = $this;

$installer->updateAttribute(
    Mage_Catalog_Model_Product::ENTITY,
    'special_price',
    'note',
    'The Special Price is active only when lower than the Actual Price',
);
