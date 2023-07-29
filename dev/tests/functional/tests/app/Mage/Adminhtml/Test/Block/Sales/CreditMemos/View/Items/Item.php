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

namespace Mage\Adminhtml\Test\Block\Sales\CreditMemos\View\Items;

use Mage\Adminhtml\Test\Block\Sales\Order\AbstractItems\AbstractItem;

/**
 * Credit memo Items block on Credit memo view page.
 */
class Item extends AbstractItem
{
    /**
     * Columns in grid.
     *
     * @var array
     */
    protected $columns = [
        'product' => ['col_name' => 'Product'],
        'item_qty' => ['col_name' => 'Qty'],
        'item_price' => ['col_name' => 'Price'],
        'item_subtotal' => ['col_name' => 'Subtotal'],
        'item_tax' => ['col_name' => 'Tax Amount'],
        'item_discount' => ['col_name' => 'Discount Amount'],
        'item_row_total' => ['col_name' => 'Row Total']
    ];
}
