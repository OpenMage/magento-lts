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

use Mage\Sales\Test\Page\SalesGuestView;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Click on "Print Order" button.
 */
class PrintOrderOnFrontendStep implements TestStepInterface
{
    /**
     * View orders page.
     *
     * @var SalesGuestView
     */
    protected $salesGuestView;

    /**
     * Browser.
     *
     * @var BrowserInterface
     */
    protected $browser;

    /**
     * @constructor
     * @param SalesGuestView $salesGuestView
     * @param BrowserInterface $browser
     */
    public function __construct(SalesGuestView $salesGuestView, BrowserInterface $browser)
    {
        $this->salesGuestView = $salesGuestView;
        $this->browser = $browser;
    }

    /**
     * Click on "Print Order" button.
     *
     * @return void
     */
    public function run()
    {
        $this->salesGuestView->getActionsToolbar()->clickLink('Print Order');
        $this->browser->selectWindow();
    }
}
