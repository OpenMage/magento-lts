<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogInventory
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

foreach ([
    'cataloginventory/options/min_qty'          => 'cataloginventory/item_options/min_qty',
    'cataloginventory/options/min_sale_qty'     => 'cataloginventory/item_options/min_sale_qty',
    'cataloginventory/options/max_sale_qty'     => 'cataloginventory/item_options/max_sale_qty',
    'cataloginventory/options/backorders'       => 'cataloginventory/item_options/backorders',
    'cataloginventory/options/notify_stock_qty' => 'cataloginventory/item_options/notify_stock_qty',
    'cataloginventory/options/manage_stock'     => 'cataloginventory/item_options/manage_stock',
] as $was => $become
) {
    $installer->run(sprintf(
        "UPDATE `%s` SET `path` = '%s' WHERE `path` = '%s'",
        $this->getTable('core/config_data'),
        $become,
        $was,
    ));
}

$installer->endSetup();
