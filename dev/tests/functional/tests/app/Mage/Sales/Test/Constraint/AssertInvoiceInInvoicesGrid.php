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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

use Magento\Mtf\ObjectManager;
use Mage\Adminhtml\Test\Block\Widget\Grid;
use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\Adminhtml\SalesInvoice;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * Assert that invoice with corresponding order ID is present in the invoices grid with corresponding amount.
 */
class AssertInvoiceInInvoicesGrid extends AbstractAssertSalesEntityInSalesEntityGrid
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
     * Error message.
     *
     * @var string
     */
    protected $errorMessage = 'Invoice is absent in invoice grid on invoice index page.';

    /**
     * Specials filter fields for shipment.
     *
     * @var array
     */
    protected $specialFilterFields = [
        'grandTotal' => [
            'from',
            'to'
        ]
    ];

    /**
     * @constructor
     * @param ObjectManager $objectManager
     * @param EventManagerInterface $eventManager
     * @param SalesInvoice $salesInvoice
     */
    public function __construct(
        ObjectManager $objectManager,
        EventManagerInterface $eventManager,
        SalesInvoice $salesInvoice)
    {
        parent::__construct($objectManager, $eventManager);
        $this->salesEntityIndexPage = $salesInvoice;
    }

    /**
     * Check visible item in grid.
     *
     * @param Grid $grid
     * @param array $filter
     * @return bool
     */
    protected function isItemInGridVisible($grid, array $filter)
    {
        return $grid->isRowVisible($filter, true, false);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Invoice is present in the invoice grid with correct total qty on invoice index page.';
    }
}
