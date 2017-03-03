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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Block\Order\Invoices\Invoice\Items;

/**
 * Item product form on invoice items block.
 */
class Product extends \Mage\Sales\Test\Block\Order\AbstractSalesEntities\SalesEntity\Items\Product
{
    /**
     * Columns in grid.
     *
     * @var array
     */
    protected $columns = [
        'product' => ['col_name' => 'Product Name'],
        'product_sku' => ['col_name' => 'SKU'],
        'item_price' => ['col_name' => 'Price'],
        'item_qty' => ['col_name' => 'Qty Invoiced', 'replace' => ['Invoiced:']],
        'item_subtotal' => ['col_name' => 'Subtotal']
    ];
}
