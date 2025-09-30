<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * API2 filter ACL attribute resource collection model
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Filter_Attribute_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('api2/acl_filter_attribute');
    }

    /**
     * Add filtering by user type
     *
     * @param string $userType
     * @return $this
     */
    public function addFilterByUserType($userType)
    {
        $this->addFilter('user_type', $userType, 'public');
        return $this;
    }
}
