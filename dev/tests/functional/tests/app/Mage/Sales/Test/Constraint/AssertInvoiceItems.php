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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Mage\Sales\Test\Page\Adminhtml\SalesInvoiceView;
use Mage\Sales\Test\Page\Adminhtml\SalesInvoice;
use Magento\Mtf\ObjectManager;

/**
 * Assert invoice items on invoice view page.
 */
class AssertInvoiceItems extends AbstractAssertItems
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Entity type.
     *
     * @var string
     */
    protected $entityType = 'invoice';

    /**
     * Special fields for verify.
     *
     * @var array
     */
    protected $specialFields = [
        'item_price',
        'item_subtotal',
        'item_tax',
        'item_discount',
        'item_row_total'
    ];

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param SalesInvoice $invoiceIndex
     * @param SalesInvoiceView $orderInvoiceView
     */
    public function __construct(
        ObjectManager $objectManager,
        SalesInvoice $invoiceIndex,
        SalesInvoiceView $orderInvoiceView
    ) {
        parent::__construct($objectManager);
        $this->salesTypePage = $invoiceIndex;
        $this->salesTypeViewPage = $orderInvoiceView;
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'All invoices products are present in invoice page.';
    }
}
