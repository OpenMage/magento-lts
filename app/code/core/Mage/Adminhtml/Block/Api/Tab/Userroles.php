<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Api_Tab_Userroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $uid = $this->getRequest()->getParam('id', false);
        $uid = !empty($uid) ? $uid : 0;
        $roles = Mage::getModel('api/roles')
            ->getCollection()
            ->load();

        $userRoles = Mage::getModel('api/roles')
            ->getUsersCollection()
            ->setUserFilter($uid)
            ->load();

        $this->setTemplate('api/userroles.phtml')
            ->assign('roles', $roles)
            ->assign('user_roles', $userRoles);
    }
}
