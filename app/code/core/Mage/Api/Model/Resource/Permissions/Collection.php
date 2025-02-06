<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Permissions Resource Collection
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Resource_Permissions_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('api/rules');
    }
}
