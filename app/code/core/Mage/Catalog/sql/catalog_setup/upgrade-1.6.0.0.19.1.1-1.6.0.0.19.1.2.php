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
    'special_price',
    'note',
    'The Special Price is active only when lower than the Actual Price',
);
