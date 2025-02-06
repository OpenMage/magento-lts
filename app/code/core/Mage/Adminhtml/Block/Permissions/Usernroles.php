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
class Mage_Adminhtml_Block_Permissions_UsernRoles extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $userCollection = Mage::getModel('permissions/users')->getCollection()->load();
        $rolesCollection = Mage::getModel('permissions/roles')->getCollection()->load();

        $this->setTemplate('permissions/usernroles.phtml')
            ->assign('users', $userCollection)
            ->assign('roles', $rolesCollection);
    }
}
