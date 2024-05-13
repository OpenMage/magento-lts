<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Block\Order\CreditMemos\CreditMemo\Items;

/**
 * Item product form on credit memo items block.
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
        'item_qty' => ['col_name' => 'Qty'],
        'item_subtotal' => ['col_name' => 'Subtotal'],
        'item_discount' => ['col_name' => 'Discount Amount'],
        'item_row_total' => ['col_name' => 'Row Total'],
    ];
}
