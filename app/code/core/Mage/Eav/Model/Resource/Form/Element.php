<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Eav
 */

/**
 * Eav Form Element Resource Model
 *
 * @category   Mage
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
