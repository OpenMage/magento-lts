<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Eav Form Type Resource Collection
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Resource_Form_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('eav/form_type');
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('type_id', 'label');
    }

    /**
     * Add Entity type filter to collection
     *
     * @param  int|Mage_Eav_Model_Entity_Type $entity
     * @return $this
     */
    public function addEntityTypeFilter($entity)
    {
        if ($entity instanceof Mage_Eav_Model_Entity_Type) {
            $entity = $entity->getId();
        }

        $this->getSelect()
            ->join(
                ['form_type_entity' => $this->getTable('eav/form_type_entity')],
                'main_table.type_id = form_type_entity.type_id',
                [],
            )
            ->where('form_type_entity.entity_type_id = ?', $entity);

        return $this;
    }
}
