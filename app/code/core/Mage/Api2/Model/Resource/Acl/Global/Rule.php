<?php
/**
 * API2 global ACL rule resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Global_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('api2/acl_rule', 'entity_id');
    }
}
