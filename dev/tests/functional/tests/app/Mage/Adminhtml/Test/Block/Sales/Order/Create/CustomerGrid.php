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

namespace Mage\Adminhtml\Test\Block\Sales\Order\Create;

use Mage\Adminhtml\Test\Block\Widget\Grid;
use Mage\Customer\Test\Fixture\Customer;

/**
 * Customer selection grid.
 */
class CustomerGrid extends Grid
{
    /**
     * Selector for 'Create New Customer' button.
     *
     * @var string
     */
    protected $createNewCustomer = '.add';

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td';

    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'email' => [
            'selector' => '#sales_order_create_customer_grid_filter_email',
        ],
    ];

    /**
     * Select customer if it is specified or click create new customer button.
     *
     * @param Customer $customer
     * @return void
     */
    public function selectCustomer(Customer $customer)
    {
        if ($customer === null) {
            $this->_rootElement->find($this->createNewCustomer)->click();
        } else {
            $this->searchAndOpen(['email' => $customer->getEmail()]);
        }
        $this->getTemplateBlock()->waitLoader();
    }
}
