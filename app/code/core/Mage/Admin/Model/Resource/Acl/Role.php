<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * ACL role resource
 *
 * @deprecated
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Resource_Acl_Role extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }
}
