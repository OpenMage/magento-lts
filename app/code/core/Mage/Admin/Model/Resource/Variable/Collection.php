<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin permissions variable collection
 *
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
