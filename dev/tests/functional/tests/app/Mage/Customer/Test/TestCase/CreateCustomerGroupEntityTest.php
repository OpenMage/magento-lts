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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\TestCase;

use Mage\Customer\Test\Fixture\CustomerGroup;
use Mage\Customer\Test\Page\Adminhtml\CustomerGroupIndex;
use Mage\Customer\Test\Page\Adminhtml\CustomerGroupNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * 1. Log in to backend as admin user.
 * 2. Navigate to Customers > Customer Groups.
 * 3. Start to create new Customer Group.
 * 4. Fill in all data according to data set.
 * 5. Click "Save Customer Group" button.
 * 6. Perform all assertions.
 *
 * @group Customer_Groups_(CS)
 * @ZephyrId MPERF-7420
 */
class CreateCustomerGroupEntityTest extends Injectable
{
    /**
     * Customer group index page.
     *
     * @var CustomerGroupIndex
     */
    protected $customerGroupIndex;

    /**
     * New customer group page.
     *
     * @var CustomerGroupNew
     */
    protected $customerGroupNew;

    /**
     * Inject data.
     *
     * @param CustomerGroupIndex $customerGroupIndex
     * @param CustomerGroupNew $customerGroupNew
     */
    public function __inject(
        CustomerGroupIndex $customerGroupIndex,
        CustomerGroupNew $customerGroupNew
    ) {
        $this->customerGroupIndex = $customerGroupIndex;
        $this->customerGroupNew = $customerGroupNew;
    }

    /**
     * Create customer group.
     *
     * @param CustomerGroup $customerGroup
     * @return void
     */
    public function test(CustomerGroup $customerGroup)
    {
        // Steps
        $this->customerGroupIndex->open();
        $this->customerGroupIndex->getGridPageActions()->addNew();
        $this->customerGroupNew->getCustomerGroupForm()->fill($customerGroup);
        $this->customerGroupNew->getPageActionsBlock()->save();
    }
}
