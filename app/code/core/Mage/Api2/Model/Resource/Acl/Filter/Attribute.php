<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * API2 filter ACL attribute resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Filter_Attribute extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Attribute Filter resource ID "all"
     */
    public const FILTER_RESOURCE_ALL = 'all';

    protected function _construct()
    {
        $this->_init('api2/acl_attribute', 'entity_id');
    }

    /**
     * @param string $userType
     * @param string $resourceId
     * @param string $operation One of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return string|bool|null
     */
    public function getAllowedAttributes($userType, $resourceId, $operation)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'allowed_attributes')
            ->where('user_type = ?', $userType)
            ->where('resource_id = ?', $resourceId)
            ->where('operation = ?', $operation);

        return $this->getReadConnection()->fetchOne($select);
    }

    /**
     * Check if ALL attributes allowed
     *
     * @param string $userType
     * @return bool
     */
    public function isAllAttributesAllowed($userType)
    {
        $resourceId = self::FILTER_RESOURCE_ALL;

        $select = $this->getReadConnection()->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(*)'))
            ->where('user_type = ?', $userType)
            ->where('resource_id = ?', $resourceId);

        return ($this->getReadConnection()->fetchOne($select) == 1);
    }
}
