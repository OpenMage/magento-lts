<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Block_Adminhtml_Tab_Userroles extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $uid = $this->getRequest()->getParam('id', false);
        $uid = !empty($uid) ? $uid : 0;
        $roles = Mage::getModel('api/roles')
            ->getCollection()
            ->load();

        $user_roles = Mage::getModel('api/roles')
            ->getUsersCollection()
            ->setUserFilter($uid)
            ->load();

        $this->setTemplate('api/userroles.phtml')
            ->assign('roles', $roles)
            ->assign('user_roles', $user_roles);
    }
}
