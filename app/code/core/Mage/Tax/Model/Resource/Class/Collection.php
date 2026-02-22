<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax class collection
 *
 * @package    Mage_Tax
 *
 * @method Mage_Tax_Model_Class[] getItems()
 */
class Mage_Tax_Model_Resource_Class_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/class');
    }

    /**
     * Add class type filter to result
     *
     * @param  string $classTypeId
     * @return $this
     */
    public function setClassTypeFilter($classTypeId)
    {
        return $this->addFieldToFilter('main_table.class_type', $classTypeId);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('class_id', 'class_name');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('class_id', 'class_name');
    }
}
