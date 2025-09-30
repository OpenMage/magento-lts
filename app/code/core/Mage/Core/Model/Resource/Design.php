<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Design Resource Model
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Design extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/design_change', 'design_change_id');
    }

    /**
     * @param Mage_Core_Model_Design $object
     * @inheritDoc
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $dateFrom = $object->getDateFrom();
        $dateTo = $object->getDateTo();
        if (!empty($dateFrom) && !empty($dateTo)) {
            $validator = new Zend_Validate_Date();
            if (!$validator->isValid($dateFrom) || !$validator->isValid($dateTo)) {
                Mage::throwException(Mage::helper('core')->__('Invalid date'));
            }
            if (Varien_Date::toTimestamp($dateFrom) > Varien_Date::toTimestamp($dateTo)) {
                Mage::throwException(Mage::helper('core')->__('Start date cannot be greater than end date.'));
            }
        }

        $check = $this->_checkIntersection(
            $object->getStoreId(),
            $dateFrom,
            $dateTo,
            $object->getId(),
        );

        if ($check) {
            Mage::throwException(
                Mage::helper('core')->__('Your design change for the specified store intersects with another one, please specify another date range.'),
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * Check intersections
     *
     * @param int $storeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param int $currentId
     * @return string
     */
    protected function _checkIntersection($storeId, $dateFrom, $dateTo, $currentId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from(['main_table' => $this->getTable('design_change')])
            ->where('main_table.store_id = :store_id')
            ->where('main_table.design_change_id <> :current_id');

        $dateConditions = ['date_to IS NULL AND date_from IS NULL'];

        if (!empty($dateFrom)) {
            $dateConditions[] = ':date_from BETWEEN date_from AND date_to';
            $dateConditions[] = ':date_from >= date_from and date_to IS NULL';
            $dateConditions[] = ':date_from <= date_to and date_from IS NULL';
        } else {
            $dateConditions[] = 'date_from IS NULL';
        }

        if (!empty($dateTo)) {
            $dateConditions[] = ':date_to BETWEEN date_from AND date_to';
            $dateConditions[] = ':date_to >= date_from AND date_to IS NULL';
            $dateConditions[] = ':date_to <= date_to AND date_from IS NULL';
        } else {
            $dateConditions[] = 'date_to IS NULL';
        }

        if (empty($dateFrom) && !empty($dateTo)) {
            $dateConditions[] = 'date_to <= :date_to OR date_from <= :date_to';
        }

        if (!empty($dateFrom) && empty($dateTo)) {
            $dateConditions[] = 'date_to >= :date_from OR date_from >= :date_from';
        }

        if (!empty($dateFrom) && !empty($dateTo)) {
            $dateConditions[] = 'date_from BETWEEN :date_from AND :date_to';
            $dateConditions[] = 'date_to BETWEEN :date_from AND :date_to';
        } elseif (empty($dateFrom) && empty($dateTo)) {
            $dateConditions = [];
        }

        $condition = '';
        if (!empty($dateConditions)) {
            $condition = '(' . implode(') OR (', $dateConditions) . ')';
            $select->where($condition);
        }

        $bind = [
            'store_id'   => (int) $storeId,
            'current_id' => (int) $currentId,
        ];

        if (!empty($dateTo)) {
            $bind['date_to'] = $dateTo;
        }
        if (!empty($dateFrom)) {
            $bind['date_from'] = $dateFrom;
        }

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Load changes for specific store and date
     *
     * @param int $storeId
     * @param string $date
     * @return array
     */
    public function loadChange($storeId, $date = null)
    {
        if (is_null($date)) {
            $date = Varien_Date::now();
        }

        $select = $this->_getReadAdapter()->select()
            ->from(['main_table' => $this->getTable('design_change')])
            ->where('store_id = :store_id')
            ->where('date_from <= :required_date or date_from IS NULL')
            ->where('date_to >= :required_date or date_to IS NULL');

        $bind = [
            'store_id'      => (int) $storeId,
            'required_date' => $date,
        ];

        return $this->_getReadAdapter()->fetchRow($select, $bind);
    }
}
