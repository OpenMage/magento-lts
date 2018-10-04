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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\TestStep;

use Mage\Cms\Test\Page\CmsIndex;
use Mage\Customer\Test\Page\CustomerAccountLogout;
use Mage\Sales\Test\Fixture\Order;
use Mage\Sales\Test\Page\SalesGuestForm;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Open sales order page on frontend for guest.
 */
class OpenSalesOrderOnFrontendForGuestStep implements TestStepInterface
{
    /**
     * Customer log out page.
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Sales guest page.
     *
     * @var SalesGuestForm
     */
    protected $salesGuestForm;

    /**
     * Fixture order.
     *
     * @var Order
     */
    protected $order;

    /**
     * @constructor
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CmsIndex $cmsIndex
     * @param SalesGuestForm $salesGuestForm
     * @param Order $order
     */
    public function __construct(
        CustomerAccountLogout $customerAccountLogout,
        CmsIndex $cmsIndex,
        SalesGuestForm $salesGuestForm,
        Order $order
    ) {
        $this->customerAccountLogout = $customerAccountLogout;
        $this->cmsIndex = $cmsIndex;
        $this->salesGuestForm = $salesGuestForm;
        $this->order = $order;
    }

    /**
     * Run step.
     *
     * @return void
     */
    public function run()
    {
        $this->customerAccountLogout->open();
        $this->cmsIndex->getFooterBlock()->clickLink('Orders and Returns');
        $this->salesGuestForm->getSearchForm()->fill($this->order);
        $this->salesGuestForm->getSearchForm()->submit();
    }
}
