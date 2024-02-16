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

use Mage\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Click "Reorder" button on order view page.
 */
class ReorderOrderStep implements TestStepInterface
{
    /**
     * Order View Page.
     *
     * @var SalesOrderView
     */
    protected $salesOrderView;

    /**
     * @construct
     * @param SalesOrderView $salesOrderView
     */
    public function __construct(SalesOrderView $salesOrderView)
    {
        $this->salesOrderView = $salesOrderView;
    }

    /**
     * Click reorder.
     *
     * @return void
     */
    public function run()
    {
        $this->salesOrderView->getPageActions()->reorder();
    }
}
