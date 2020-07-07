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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
