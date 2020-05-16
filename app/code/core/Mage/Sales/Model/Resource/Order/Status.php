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
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order status resource model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Status labels table
     *
     * @var string
     */
    protected $_labelsTable;

    /**
     * Status state table
     *
     * @var string
     */
    protected $_stateTable;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('sales/order_status', 'status');
        $this->_isPkAutoIncrement = false;
        $this->_labelsTable = $this->getTable('sales/order_status_label');
        $this->_stateTable  = $this->getTable('sales/order_status_state');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Varien_Object $object
     * @return  Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        if ($field == 'default_state') {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), array('label'))
                ->join(
                    array('state_table' => $this->_stateTable),
                    $this->getMainTable() . '.status = state_table.status',
                    'status'
                )
                ->where('state_table.state = ?', $value)
                ->order('state_table.is_default DESC')
                ->limit(1);
        } else {
            $select = parent::_getLoadSelect($field, $value, $object);
        }
        return $select;
    }

    /**
     * Store labels getter
     *
     * @param Mage_Core_Model_Abstract|Mage_Sales_Model_Order_Status $status
     * @return array
     */
    public function getStoreLabels(Mage_Core_Model_Abstract $status)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_labelsTable, array('store_id', 'label'))
            ->where('status = ?', $status->getStatus());
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Save status labels per store
     *
     * @param Mage_Sales_Model_Order|Mage_Sales_Model_Order_Status $object
     * @inheritDoc
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasStoreLabels()) {
            $labels = $object->getStoreLabels();
            $this->_getWriteAdapter()->delete(
                $this->_labelsTable,
                array('status = ?' => $object->getStatus())
            );
            $data = array();
            foreach ($labels as $storeId => $label) {
                if (empty($label)) {
                    continue;
                }
                $data[] = array(
                    'status'    => $object->getStatus(),
                    'store_id'  => $storeId,
                    'label'     => $label
                );
            }
            if (!empty($data)) {
                $this->_getWriteAdapter()->insertMultiple($this->_labelsTable, $data);
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Assign order status to order state
     *
     * @param string $status
     * @param string $state
     * @param bool $isDefault
     * @return $this
     */
    public function assignState($status, $state, $isDefault)
    {
        if ($isDefault) {
            $this->_getWriteAdapter()->update(
                $this->_stateTable,
                array('is_default' => 0),
                array('state = ?' => $state)
            );
        }
        $this->_getWriteAdapter()->insertOnDuplicate(
            $this->_stateTable,
            array(
                'status'     => $status,
                'state'      => $state,
                'is_default' => (int) $isDefault
            )
        );
        return $this;
    }

    /**
     * Unassign order status from order state
     *
     * @param string $status
     * @param string $state
     * @return $this
     */
    public function unassignState($status, $state)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_stateTable, array('qty' => new Zend_Db_Expr('COUNT(*)')))
            ->where('state = ?', $state);

        if ($this->_getWriteAdapter()->fetchOne($select) == 1) {
            throw new Mage_Core_Exception(
                Mage::helper('sales')->__('Last status can\'t be unassigned from state.')
            );
        }
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_stateTable, 'is_default')
            ->where('state = ?', $state)
            ->where('status = ?', $status)
            ->limit(1);
        $isDefault = $this->_getWriteAdapter()->fetchOne($select);
        $this->_getWriteAdapter()->delete(
            $this->_stateTable,
            array(
                'state = ?' => $state,
                'status = ?' => $status
            )
        );

        if ($isDefault) {
            $select = $this->_getWriteAdapter()->select()
                ->from($this->_stateTable, 'status')
                ->where('state = ?', $state)
                ->limit(1);
            $defaultStatus = $this->_getWriteAdapter()->fetchOne($select);
            if ($defaultStatus) {
                $this->_getWriteAdapter()->update(
                    $this->_stateTable,
                    array('is_default' => 1),
                    array(
                        'state = ?' => $state,
                        'status = ?' => $defaultStatus
                    )
                );
            }
        }
        return $this;
    }
}
