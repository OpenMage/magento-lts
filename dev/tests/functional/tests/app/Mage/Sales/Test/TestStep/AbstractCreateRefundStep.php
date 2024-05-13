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

namespace Mage\Sales\Test\TestStep;

use Mage\Sales\Test\Page\Adminhtml\SalesOrderCreditMemoNew;
use Mage\Sales\Test\Page\Adminhtml\SalesInvoiceView;

/**
 * Abstract creating refund from order on backend.
 */
class AbstractCreateRefundStep extends AbstractCreateSalesEntityStep
{
    /**
     * Sales entity type.
     *
     * @var string
     */
    protected $entityType = 'refund';

    /**
     * Sales invoice view page.
     *
     * @var SalesInvoiceView
     */
    protected $invoiceViewPage;

    /**
     * Current invoice id.
     *
     * @var string
     */
    protected $currentInvoiceId;

    /**
     * Get sales entity new page.
     *
     * @return SalesOrderCreditMemoNew
     */
    protected function getSalesEntityNewPage()
    {
        return $this->objectManager->create('Mage\Sales\Test\Page\Adminhtml\SalesOrderCreditMemoNew');
    }

    /**
     * Create offline refund.
     *
     * @return void
     */
    protected function salesEntityAction()
    {
        $orderForm = $this->salesOrderView->getOrderForm();
        $orderForm->openTab('invoices');
        $this->invoiceViewPage = $this->objectManager->create('Mage\Sales\Test\Page\Adminhtml\SalesInvoiceView');
        $orderForm->getTabElement('invoices')->getGrid()->searchAndOpen(['id' => $this->currentInvoiceId]);
        $this->invoiceViewPage->getPageActions()->creditMemo();
    }

    /**
     * Process offline credit memo step.
     *
     * @param int $index [optional]
     * @return void
     */
    protected function processStep($index = 0)
    {
        $invoiceIds =  $this->getEntityIds('invoice');
        foreach ($invoiceIds as $key => $id){
            $this->currentInvoiceId = $id;
            parent::processStep($key);
        }
    }
}
