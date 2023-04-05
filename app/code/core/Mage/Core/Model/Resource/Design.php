<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Core Design Resource Model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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
            $object->getId()
        );

        if ($check) {
            Mage::throwException(
                Mage::helper('core')->__('Your design change for the specified store intersects with another one, please specify another date range.')
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
            'store_id'   => (int)$storeId,
            'current_id' => (int)$currentId,
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
            'store_id'      => (int)$storeId,
            'required_date' => $date
        ];

        return $this->_getReadAdapter()->fetchRow($select, $bind);
    }
}
