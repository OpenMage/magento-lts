<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Element Resource Model
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Form_Element extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/form_element', 'element_id');
        $this->addUniqueField([
            'field' => ['type_id', 'attribute_id'],
            'title' => Mage::helper('eav')->__('Form Element with the same attribute'),
        ]);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Eav_Model_Form_Element $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->join(
            $this->getTable('eav/attribute'),
            $this->getTable('eav/attribute') . '.attribute_id = ' . $this->getMainTable() . '.attribute_id',
            ['attribute_code', 'entity_type_id'],
        );

        return $select;
    }
}
