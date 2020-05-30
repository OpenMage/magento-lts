<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Eav Form Type Resource Model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Form_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('eav/form_type', 'type_id');
        $this->addUniqueField(array(
            'field' => array('code', 'theme', 'store_id'),
            'title' => Mage::helper('eav')->__('Form Type with the same code')
        ));
    }

    /**
     * Load an object
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_null($field) && !is_numeric($value)) {
            $field = 'code';
        }
        return parent::load($object, $value, $field);
    }

    /**
     * Retrieve form type entity types
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @return array
     */
    public function getEntityTypes($object)
    {
        $objectId = $object->getId();
        if (!$objectId) {
            return array();
        }
        $adapter = $this->_getReadAdapter();
        $bind    = array(':type_id' => $objectId);
        $select  = $adapter->select()
            ->from($this->getTable('eav/form_type_entity'), 'entity_type_id')
            ->where('type_id = :type_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Save entity types after save form type
     *
     * @see Mage_Core_Model_Resource_Db_Abstract#_afterSave($object)
     *
     * @param Mage_Eav_Model_Form_Type $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasEntityTypes()) {
            $new = $object->getEntityTypes();
            $old = $this->getEntityTypes($object);

            $insert = array_diff($new, $old);
            $delete = array_diff($old, $new);

            $adapter  = $this->_getWriteAdapter();

            if (!empty($insert)) {
                $data = array();
                foreach ($insert as $entityId) {
                    if (empty($entityId)) {
                        continue;
                    }
                    $data[] = array(
                        'entity_type_id' => (int)$entityId,
                        'type_id'        => $object->getId()
                    );
                }
                if ($data) {
                    $adapter->insertMultiple($this->getTable('eav/form_type_entity'), $data);
                }
            }

            if (!empty($delete)) {
                $where = array(
                    'entity_type_id IN (?)' => $delete,
                    'type_id = ?'           => $object->getId()
                );
                $adapter->delete($this->getTable('eav/form_type_entity'), $where);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve form type filtered by given attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|int $attribute
     * @return array
     */
    public function getFormTypesByAttribute($attribute)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $attribute = $attribute->getId();
        }
        if (!$attribute) {
            return array();
        }
        $bind   = array(':attribute_id' => $attribute);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav/form_element'))
            ->where('attribute_id = :attribute_id');

        return $this->_getReadAdapter()->fetchAll($select, $bind);
    }
}
