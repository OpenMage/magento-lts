<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
foreach (array(
    'cataloginventory/options/min_qty'          => 'cataloginventory/item_options/min_qty',
    'cataloginventory/options/min_sale_qty'     => 'cataloginventory/item_options/min_sale_qty',
    'cataloginventory/options/max_sale_qty'     => 'cataloginventory/item_options/max_sale_qty',
    'cataloginventory/options/backorders'       => 'cataloginventory/item_options/backorders',
    'cataloginventory/options/notify_stock_qty' => 'cataloginventory/item_options/notify_stock_qty',
    'cataloginventory/options/manage_stock'     => 'cataloginventory/item_options/manage_stock',
    ) as $was => $become) {
    $installer->run(sprintf("UPDATE `%s` SET `path` = '%s' WHERE `path` = '%s'",
        $this->getTable('core/config_data'), $become, $was
    ));
}

$installer->endSetup();
