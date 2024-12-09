<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $was
    ));
}

$installer->endSetup();
