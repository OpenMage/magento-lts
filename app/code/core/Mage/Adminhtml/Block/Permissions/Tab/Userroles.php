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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Tab_Userroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $uid = $this->getRequest()->getParam('id', false);
        $uid = empty($uid) ? 0 : $uid;
        $roles = Mage::getModel("admin/roles")
            ->getCollection()
            ->load();

        $user_roles = Mage::getModel("admin/roles")
            ->getUsersCollection()
            ->setUserFilter($uid)
            ->load();

        $this->setTemplate('permissions/userroles.phtml')
            ->assign('roles', $roles)
            ->assign('user_roles', $user_roles);
    }
}
