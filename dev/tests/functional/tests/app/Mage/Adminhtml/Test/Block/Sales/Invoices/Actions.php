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
