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

namespace Mage\Adminhtml\Test\Handler\Ui;

use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Magento\Mtf\Fixture\FixtureInterface;
use Magento\Mtf\Handler\Ui;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;

/**
 * Handler for ui backend user login.
 */
class LoginUser extends Ui
{
    /**
     * Admin auth login page.
     *
     * @var AdminAuthLogin
     */
    protected $adminAuthLogin;

    /**
     * Dashboard page.
     *
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * @constructor
     * @param AdminAuthLogin $adminAuthLogin
     * @param Dashboard $dashboard
     */
    public function __construct(AdminAuthLogin $adminAuthLogin, Dashboard $dashboard)
    {
        $this->adminAuthLogin = $adminAuthLogin;
        $this->dashboard = $dashboard;
    }

    /**
     * Login admin user.
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->adminAuthLogin->open();
        $loginBlock = $this->adminAuthLogin->getLoginBlock();
        if ($loginBlock->isVisible()) {
            $loginBlock->fill($fixture);
            $loginBlock->submit();
            $this->dashboard->getAdminPanelHeader()->waitVisible();
        }
    }
}
