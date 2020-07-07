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

namespace Mage\Admin\Test\Constraint;

use Mage\Admin\Test\Fixture\Role;
use Mage\Admin\Test\Fixture\User;
use Mage\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Mage\Adminhtml\Test\Page\AdminAuthLogin;

/**
 * Assert that user have custom role.
 */
class AssertUserWithCustomRole extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that user have custom role.
     *
     * @param User $user
     * @param AdminAuthLogin $adminAuth
     * @param Dashboard $dashboard
     * @param CatalogCategoryIndex $categoryIndex
     * @param User $customAdmin [optional]
     * @return void
     */
    public function processAssert(
        User $user,
        AdminAuthLogin $adminAuth,
        Dashboard $dashboard,
        CatalogCategoryIndex $categoryIndex,
        User $customAdmin = null
    ) {
        $adminUser = $customAdmin === null ? $user : $customAdmin;
        $adminPanelHeader = $dashboard->getAdminPanelHeader();
        if ($adminPanelHeader->isVisible()) {
            $adminPanelHeader->logOut();
        }
        $adminAuth->getLoginBlock()->loginToAdminPanel($adminUser->getData());

        $role = $user->getDataFieldConfig('role_id')['source']->getRole();
        $menuItems = $this->getMenuItems($role);
        foreach ($menuItems as $item) {
            \PHPUnit_Framework_Assert::assertTrue(
                $adminPanelHeader->checkMenu(ucfirst($item)),
                "Menu $item is absent on top menu."
            );
        }

        // Check access denied message on category page.
        $categoryIndex->open();
        \PHPUnit_Framework_Assert::assertTrue($categoryIndex->getContentBlock()->checkAccessDeniedMessage());
    }

    /**
     * Get menu items.
     *
     * @param Role $role
     * @return array
     */
    protected function getMenuItems(Role $role)
    {
        $resources = $role->getRolesResources();
        $rootMenu = [];
        foreach ($resources as $resource) {
            $elements = explode('/', $resource);
            if (!in_array($elements[0], $rootMenu)) {
                $rootMenu[] = $elements[0];
            }
        }
        return $rootMenu;
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'User have custom role.';
    }
}
