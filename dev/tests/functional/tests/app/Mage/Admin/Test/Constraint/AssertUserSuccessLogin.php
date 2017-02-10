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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Admin\Test\Constraint;

use Mage\Admin\Test\Fixture\User;
use Magento\Mtf\Client\Browser;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;

/**
 * Verify whether customer has logged in to the Backend.
 */
class AssertUserSuccessLogin extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Verify whether customer has logged in to the Backend.
     *
     * @param User $user
     * @param AdminAuthLogin $adminAuth
     * @param Dashboard $dashboard
     * @param Browser $browser
     * @param User $customAdmin
     * @param array $install [optional]
     * @return void
     */
    public function processAssert(
        User $user,
        AdminAuthLogin $adminAuth,
        Dashboard $dashboard,
        Browser $browser,
        User $customAdmin = null,
        $install = []
    ) {
        $adminUser = $customAdmin === null ? $user : $customAdmin;
        $adminPanelHeader = $dashboard->getAdminPanelHeader();
        if ($adminPanelHeader->isVisible()) {
            $adminPanelHeader->logOut();
        }
        if (!$adminAuth->getLoginBlock()->isVisible()) {
            $this->checkForInstallData($browser, $install);
        }
        $adminAuth->getLoginBlock()->loginToAdminPanel($adminUser->getData());

        \PHPUnit_Framework_Assert::assertTrue($adminPanelHeader->isVisible(), 'Admin user was not logged in.');
    }

    /**
     * Determines if assert is called after magento installation and performs assert precondition.
     *
     * @param Browser $browser
     * @param array $install
     * @return void
     */
    protected function checkForInstallData(Browser $browser, array $install)
    {
        isset($install['admin_frontname'])
            ? $browser->open($_ENV['app_frontend_url'] . $install['admin_frontname'])
            : $browser->open($_ENV['app_backend_url']);
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Admin user is logged in.';
    }
}
