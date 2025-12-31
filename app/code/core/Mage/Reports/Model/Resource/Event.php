<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Report events resource model
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Event extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize main table and identifier field.
     * Set main entity table name and primary key field name.
     */
    protected function _construct()
    {
        $this->_init('reports/event', 'event_id');
    }

    /**
     * Update customer type after customer login
     *
     * @param  int   $visitorId
     * @param  int   $customerId
     * @param  array $types
     * @return $this
     */
    public function updateCustomerType(Mage_Reports_Model_Event $model, $visitorId, $customerId, $types = [])
    {
        if ($types) {
            $this->_getWriteAdapter()->update(
                $this->getMainTable(),
                ['subject_id' => (int) $customerId, 'subtype' => 0],
                [
                    'subject_id = ?'      => (int) $visitorId,
                    'subtype = ?'         => 1,
                    'event_type_id IN(?)' => $types,
                ],
            );
        }

        return $this;
    }

    /**
     * Add events log to a collection
     * The collection id field is used without correlation, so it must be unique.
     * DESC ordering by event will be added to the collection
     *
     * @param  int   $eventTypeId
     * @param  int   $eventSubjectId
     * @param  int   $subtype
     * @param  array $skipIds
     * @return $this
     */
    public function applyLogToCollection(
        Varien_Data_Collection_Db $collection,
        $eventTypeId,
        $eventSubjectId,
        $subtype,
        $skipIds = []
    ) {
        $idFieldName = $collection->getResource()->getIdFieldName();

        $derivedSelect = $this->getReadConnection()->select()
            ->from(
                $this->getTable('reports/event'),
                ['event_id' => new Zend_Db_Expr('MAX(event_id)'), 'object_id'],
            )
            ->where('event_type_id = ?', (int) $eventTypeId)
            ->where('subject_id = ?', (int) $eventSubjectId)
            ->where('subtype = ?', (int) $subtype)
            ->where('store_id IN(?)', $this->getCurrentStoreIds())
            ->group('object_id');

        if ($skipIds) {
            if (!is_array($skipIds)) {
                $skipIds = [(int) $skipIds];
            }

            $derivedSelect->where('object_id NOT IN(?)', $skipIds);
        }

        $collection->getSelect()
            ->joinInner(
                ['evt' => new Zend_Db_Expr("({$derivedSelect})")],
                "{$idFieldName} = evt.object_id",
                [],
            )
            ->order('evt.event_id ' . Varien_Db_Select::SQL_DESC);

        return $this;
    }

    /**
     * Obtain all current store ids, depending on configuration
     *
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getCurrentStoreIds(?array $predefinedStoreIds = null)
    {
        $stores = [];
        // get all or specified stores
        if (Mage::app()->getStore()->getId() == 0) {
            if ($predefinedStoreIds !== null) {
                $stores = $predefinedStoreIds;
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $stores[] = $store->getId();
                }
            }
        } else { // get all stores, required by configuration in current store scope
            $resourceStore = match (Mage::getStoreConfig('catalog/recently_products/scope')) {
                'website' => Mage::app()->getStore()->getWebsite()->getStores(),
                'group' => Mage::app()->getStore()->getGroup()->getStores(),
                default => [Mage::app()->getStore()],
            };

            foreach ($resourceStore as $store) {
                $stores[] = $store->getId();
            }
        }

        foreach ($stores as $key => $store) {
            $stores[$key] = (int) $store;
        }

        return $stores;
    }

    /**
     * Clean report event table
     *
     * @return $this
     */
    public function clean(Mage_Reports_Model_Event $object)
    {
        while (true) {
            $select = $this->_getReadAdapter()->select()
                ->from(['event_table' => $this->getMainTable()], ['event_id'])
                ->joinLeft(
                    ['visitor_table' => $this->getTable('log/visitor')],
                    'event_table.subject_id = visitor_table.visitor_id',
                    [],
                )
                ->where('visitor_table.visitor_id IS NULL')
                ->where('event_table.subtype = ?', 1)
                ->limit(1000);
            $eventIds = $this->_getReadAdapter()->fetchCol($select);

            if (!$eventIds) {
                break;
            }

            $this->_getWriteAdapter()->delete($this->getMainTable(), ['event_id IN(?)' => $eventIds]);
        }

        return $this;
    }
}
