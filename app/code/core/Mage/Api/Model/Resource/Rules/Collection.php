<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Api Rules Resource Collection
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Rules_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init('api/rules');
    }

    /**
     * Retrieve rules by role
     *
     * @param string $id
     * @return $this
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where('role_id = ?', (int) $id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return $this
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(['length' => $this->getConnection()->getLengthSql('resource_id')])
            ->order('length DESC');
        return $this;
    }
}
