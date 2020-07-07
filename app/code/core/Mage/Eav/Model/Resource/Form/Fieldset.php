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
 * Eav Form Fieldset Resource Model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Form_Fieldset extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     */
    protected function _construct()
    {
        $this->_init('eav/form_fieldset', 'fieldset_id');
        $this->addUniqueField(array(
            'field' => array('type_id', 'code'),
            'title' => Mage::helper('eav')->__('Form Fieldset with the same code')
        ));
    }

    /**
     * After save (save labels)
     *
     * @param Mage_Eav_Model_Form_Fieldset $object
     * @inheritDoc
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasLabels()) {
            $new        = $object->getLabels();
            $old        = $this->getLabels($object);

            $adapter    = $this->_getWriteAdapter();

            $insert     = array_diff(array_keys($new), array_keys($old));
            $delete     = array_diff(array_keys($old), array_keys($new));
            $update     = array();

            foreach ($new as $storeId => $label) {
                if (isset($old[$storeId]) && $old[$storeId] != $label) {
                    $update[$storeId] = $label;
                } elseif (isset($old[$storeId]) && empty($label)) {
                    $delete[] = $storeId;
                }
            }

            if (!empty($insert)) {
                $data = array();
                foreach ($insert as $storeId) {
                    $label = $new[$storeId];
                    if (empty($label)) {
                        continue;
                    }
                    $data[] = array(
                        'fieldset_id'   => (int)$object->getId(),
                        'store_id'      => (int)$storeId,
                        'label'         => $label
                    );
                }
                if ($data) {
                    $adapter->insertMultiple($this->getTable('eav/form_fieldset_label'), $data);
                }
            }

            if (!empty($delete)) {
                $where = array(
                    'fieldset_id = ?' => $object->getId(),
                    'store_id IN(?)' => $delete
                );
                $adapter->delete($this->getTable('eav/form_fieldset_label'), $where);
            }

            if (!empty($update)) {
                foreach ($update as $storeId => $label) {
                    $bind  = array('label' => $label);
                    $where = array(
                        'fieldset_id =?' => $object->getId(),
                        'store_id =?'    => $storeId
                    );
                    $adapter->update($this->getTable('eav/form_fieldset_label'), $bind, $where);
                }
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve fieldset labels for stores
     *
     * @param Mage_Eav_Model_Form_Fieldset $object
     * @return array
     */
    public function getLabels($object)
    {
        $objectId = $object->getId();
        if (!$objectId) {
            return array();
        }
        $adapter = $this->_getReadAdapter();
        $bind    = array(':fieldset_id' => $objectId);
        $select  = $adapter->select()
            ->from($this->getTable('eav/form_fieldset_label'), array('store_id', 'label'))
            ->where('fieldset_id = :fieldset_id');

        return $adapter->fetchPairs($select, $bind);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Eav_Model_Form_Fieldset $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $labelExpr = $select->getAdapter()->getIfNullSql('store_label.label', 'default_label.label');

        $select
            ->joinLeft(
                array('default_label' => $this->getTable('eav/form_fieldset_label')),
                $this->getMainTable() . '.fieldset_id = default_label.fieldset_id AND default_label.store_id=0',
                array()
            )
            ->joinLeft(
                array('store_label' => $this->getTable('eav/form_fieldset_label')),
                $this->getMainTable() . '.fieldset_id = store_label.fieldset_id AND default_label.store_id='
                    . (int)$object->getStoreId(),
                array('label' => $labelExpr)
            );

        return $select;
    }
}
