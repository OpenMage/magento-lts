<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Tax class resource
 *
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Class extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('tax/tax_class', 'class_id');
    }

    /**
     * Initialize unique fields
     *
     * @return $this
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [[
            'field' => ['class_type', 'class_name'],
            'title' => Mage::helper('tax')->__('An error occurred while saving this tax class. A class with the same name'),
        ]];
        return $this;
    }
}
