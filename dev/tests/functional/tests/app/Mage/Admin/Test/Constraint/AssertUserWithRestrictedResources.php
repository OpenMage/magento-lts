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

namespace Mage\Admin\Test\Constraint;

use Mage\Admin\Test\Fixture\User;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Not allowed resources are not available on menu level. In case if type it via URL - "Access Denied" page appeared.
 */
class AssertUserWithRestrictedResources extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Access denied text.
     */
    const ACCESS_DENIED = 'Access denied';

    /**
     * Manage catalog products url.
     *
     * @var string
     */
    protected $manageProductsUrl = 'catalog_product/';

    /**
     * Not allowed resources are not available on menu level.
     * In case if type it via URL - "Access Denied" page appeared.
     *
     * @param User $user
     * @param AdminAuthLogin $adminAuthLogin
     * @param Dashboard $dashboard
     * @param Browser $browser
     * @return void
     */
    public function processAssert(User $user, AdminAuthLogin $adminAuthLogin, Dashboard $dashboard, Browser $browser)
    {
        $adminPanelHeader = $dashboard->getAdminPanelHeader();
        if ($adminPanelHeader->isVisible()) {
            $adminPanelHeader->logOut();
        }
        $adminAuthLogin->getLoginBlock()->loginToAdminPanel($user->getData());
        $menuItems = $adminPanelHeader->getMenuFirstLevelItems();

        \PHPUnit_Framework_Assert::assertTrue(
            count($menuItems) == 1 && in_array('Sales', $menuItems),
            'Sales menu item is not visible or count of menu items is greater than 1.'
        );

        $browser->open($_ENV['app_backend_url'] . $this->manageProductsUrl);
        \PHPUnit_Framework_Assert::assertTrue(
            strpos($dashboard->getMainBlock()->getMainBlockText(), self::ACCESS_DENIED) !== false,
            self::ACCESS_DENIED . " text is not visible on dashboard page.");
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return "Not allowed resources are not available on menu level."
        . " In case if type it via URL - 'Access Denied' page appeared.";
    }
}
