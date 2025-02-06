<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * Admin permissions variable collection
 *
 * @category   Mage
 * @package    Mage_Admin
 *
 * @method Mage_Admin_Model_Variable getItemById(int $value)
 * @method Mage_Admin_Model_Variable[] getItems()
 */
class Mage_Admin_Model_Resource_Variable_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('admin/variable');
    }
}
