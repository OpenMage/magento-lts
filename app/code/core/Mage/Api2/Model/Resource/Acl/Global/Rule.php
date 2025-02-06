<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api2
 */

/**
 * API2 global ACL rule resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Resource_Acl_Global_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('api2/acl_rule', 'entity_id');
    }
}
