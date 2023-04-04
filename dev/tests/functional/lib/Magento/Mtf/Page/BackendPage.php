<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\Mtf\Page;

use Magento\Mtf\ObjectManager;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Magento\Mtf\Block\BlockFactory;
use Magento\Mtf\Client\BrowserInterface;
use Magento\Mtf\Config\DataInterface;
use Magento\Mtf\Config\Data;

/**
 * Class for backend pages.
 */
class BackendPage extends Page
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
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        $this->url = $_ENV['app_backend_url'] . static::MCA;
    }

    /**
     * Open backend page and log in if needed.
     *
     * @param array $params
     * @return $this
     */
    public function open(array $params = [])
    {
        $systemConfig = ObjectManager::getInstance()->create('Magento\Mtf\Config\DataInterface');
        $admin = [
            'username' => [
                'value' => $systemConfig->get('application/0/backendLogin/0/value')
            ],
            'password' => [
                'value' => $systemConfig->get('application/0/backendPassword/0/value')
            ]
        ];
        $this->adminAuthLogin = ObjectManager::getInstance()->create('Mage\Adminhtml\Test\Page\AdminAuthLogin');
        $this->dashboard = ObjectManager::getInstance()->create('Mage\Adminhtml\Test\Page\Adminhtml\Dashboard');

        if (!$this->dashboard->getAdminPanelHeader()->isVisible()) {
            $this->loginSuperAdmin($admin);
        }
        return parent::open($params);
    }

    /**
     * Log in on backend for super admin.
     *
     * @param array $admin
     * @return void
     */
    protected function loginSuperAdmin(array $admin)
    {
        $this->adminAuthLogin->open();
        $loginBlock = $this->adminAuthLogin->getLoginBlock();
        if ($loginBlock->isVisible()) {
            $loginBlock->loginToAdminPanel($admin);
        }
    }
}
