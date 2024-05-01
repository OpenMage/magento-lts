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
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Install\Test\Constraint;

use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;

/**
 * Assert that Secure Urls Enabled.
 */
class AssertSecureUrlEnabled extends AbstractConstraint
{
    /**
     * Assert that Secure Urls Enabled.
     *
     * @param BrowserInterface $browser
     * @param Dashboard $dashboard
     * @return void
     */
    public function processAssert(BrowserInterface $browser, Dashboard $dashboard)
    {
        $dashboard->open();
        \PHPUnit_Framework_Assert::assertTrue(
            str_contains($browser->getUrl(), 'https://'),
            'Secure Url is not displayed on backend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Secure Urls are displayed successful.';
    }
}
