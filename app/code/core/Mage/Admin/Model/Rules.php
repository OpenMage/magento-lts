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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin Rules Model
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Admin_Model_Resource_Rules _getResource()
 * @method Mage_Admin_Model_Resource_Rules getResource()
 * @method Mage_Admin_Model_Resource_Rules_Collection getResourceCollection()
 *
 * @method int getAssertId()
 * @method $this setAssertId(int $value)
 * @method string getPermission()
 * @method $this setPermission(string $value)
 * @method array getResources()
 * @method $this setResources(array $value)
 * @method string getResourceId()
 * @method $this setResourceId(string $value)
 * @method string getPrivileges()
 * @method $this setPrivileges(string $value)
 * @method int getRoleId()
 * @method $this setRoleId(int $value)
 * @method string getRoleType()
 * @method $this setRoleType(string $value)
 */
class Mage_Admin_Model_Rules extends Mage_Core_Model_Abstract
{
    /**
     * Allowed permission code
     */
    public const RULE_PERMISSION_ALLOWED = 'allow';

    /**
     * Denied permission code
     */
    public const RULE_PERMISSION_DENIED = 'deny';

    protected function _construct()
    {
        $this->_init('admin/rules');
    }

    /**
     * Update rules
     * @return $this
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Initialize and retrieve permissions collection
     * @return Object
     */
    public function getCollection()
    {
        return Mage::getResourceModel('admin/permissions_collection');
    }

    /**
     * Save rules relations to the database
     * @return $this
     */
    public function saveRel()
    {
        $this->getResource()->saveRel($this);
        return $this;
    }

    /**
     * Check if the current rule is allowed
     * @return bool
     */
    public function isAllowed()
    {
        return $this->getPermission() == self::RULE_PERMISSION_ALLOWED;
    }

    /**
     * Check if the current rule is denied
     */
    public function isDenied()
    {
        return $this->getPermission() == self::RULE_PERMISSION_DENIED;
    }
}
