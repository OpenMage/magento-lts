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

use Mage\Sales\Test\Page\Adminhtml\SalesOrderInvoiceNew;

/**
 * Create invoice from order on backend.
 */
class CreateInvoiceStep extends AbstractCreateSalesEntityStep
{
    /**
     * Sales entity type.
     *
     * @var string
     */
    protected $entityType = 'invoice';

    /**
     * Get sales entity new page.
     *
     * @return SalesOrderInvoiceNew
     */
    protected function getSalesEntityNewPage()
    {
        return $this->objectManager->create('Mage\Sales\Test\Page\Adminhtml\SalesOrderInvoiceNew');
    }
}
