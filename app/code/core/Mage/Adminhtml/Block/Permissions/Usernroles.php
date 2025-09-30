<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
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
