<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sales\Invoices;

use Magento\Mtf\Block\Block;

/**
 * Order actions block.
 */
class Actions extends Block
{
    /**
     * 'Credit Memo' button on the order page.
     *
     * @var string
     */
    protected $orderCreditMemoButton = '[onclick*="sales_order_creditmemo"]';

    /**
     * Order credit memo.
     *
     * @return void
     */
    public function creditMemo()
    {
        $this->_rootElement->find($this->orderCreditMemoButton)->click();
    }
}
