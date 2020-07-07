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
