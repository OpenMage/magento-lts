<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Convert_Adapter_Entity extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * Current store model
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store;

    protected $_filter = [];

    protected $_joinFilter = [];

    protected $_joinAttr = [];

    protected $_attrToDb;

    protected $_joinField = [];

    /**
     * Retrieve store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_store)) {
            try {
                $this->_store = Mage::app()->getStore($this->getVar('store'));
            } catch (Exception $e) {
                $message = Mage::helper('eav')->__('Invalid store specified');
                $this->addException($message, Varien_Convert_Exception::FATAL);
                throw $e;
            }
        }

        return $this->_store->getId();
    }

    /**
     * @return array
     */
    protected function _parseVars()
    {
        $varFilters = $this->getVars();
        $filters = [];
        foreach ($varFilters as $key => $val) {
            if (str_starts_with($key, 'filter')) {
                $keys = explode('/', $key, 2);
                $filters[$keys[1]] = $val;
            }
        }

        return $filters;
    }

    /**
     * @param array $attrFilterArray
     * @param array $attrToDb
     * @param string $bind
     * @param string $joinType
     * @return $this
     * @throws Exception
     */
    public function setFilter($attrFilterArray, $attrToDb = null, $bind = null, $joinType = null)
    {
        if (is_null($bind)) {
            $defBind = 'entity_id';
        }

        if (is_null($joinType)) {
            $joinType = 'LEFT';
        }

        $this->_attrToDb = $attrToDb;
        $filters = $this->_parseVars();

        foreach ($attrFilterArray as $key => $type) {
            if (is_array($type)) {
                $bind = $type['bind'] ?? $defBind;
                $type = $type['type'];
            }

            if ($type == 'dateFromTo' || $type == 'datetimeFromTo') {
                foreach ($filters as $k => $v) {
                    if (str_starts_with($k, $key . '/')) {
                        $split = explode('/', $k);
                        $filters[$key][$split[1]] = $v;
                    }
                }
            }

            $keyDB = $this->_attrToDb[$key] ?? $key;

            $exp = explode('/', $key);

            if (isset($exp[1])) {
                if (isset($filters[$exp[1]])) {
                    $val = $filters[$exp[1]];
                    $this->setJoinAttr([
                        'attribute' => $keyDB,
                        'bind' => $bind,
                        'joinType' => $joinType,
                    ]);
                } else {
                    $val = null;
                }

                $keyDB = str_replace('/', '_', $keyDB);
            } else {
                $val = $filters[$key] ?? null;
            }

            if (is_null($val)) {
                continue;
            }

            $attr = [];
            switch ($type) {
                case 'eq':
                    $attr = [
                        'attribute' => $keyDB,
                        'eq'        => $val,
                    ];
                    break;
                case 'like':
                    $attr = [
                        'attribute' => $keyDB,
                        'like'      => '%' . $val . '%',
                    ];
                    break;
                case 'startsWith':
                    $attr = [
                        'attribute' => $keyDB,
                        'like'      => $val . '%',
                    ];
                    break;
                case 'fromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'],
                        'to'        => $val['to'],
                    ];
                    break;
                case 'dateFromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'],
                        'to'        => $val['to'],
                        'date'      => true,
                    ];
                    break;
                case 'datetimeFromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'] ?? null,
                        'to'        => $val['to'] ?? null,
                        'datetime'  => true,
                    ];
                    break;
                default:
                    break;
            }

            $this->_filter[] = $attr;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * @param array $fields
     * @param string $name
     * @return array|bool
     */
    protected function getFieldValue($fields = [], $name = '')
    {
        $result = [];
        if ($fields && $name) {
            foreach ($fields as $index => $value) {
                $exp = explode('/', $index);
                if (isset($exp[1]) && $exp[0] == $name) {
                    $result[$exp[1]] = $value;
                }
            }

            if ($result) {
                return $result;
            }
        }

        return false;
    }

    /**
     * @param array $joinAttr
     * @throws Exception
     */
    public function setJoinAttr($joinAttr)
    {
        if (is_array($joinAttr)) {
            $joinArrAttr = [];
            $joinArrAttr['attribute'] = $joinAttr['attribute'] ?? null;
            $joinArrAttr['alias'] = isset($joinAttr['attribute']) ? str_replace('/', '_', $joinAttr['attribute']) : null;
            $joinArrAttr['bind'] = $joinAttr['bind'] ?? null;
            $joinArrAttr['joinType'] = $joinAttr['joinType'] ?? null;
            $joinArrAttr['storeId'] = $joinAttr['storeId'] ?? $this->getStoreId();
            $this->_joinAttr[] = $joinArrAttr;
        }
    }

    /**
     * Add join field
     *
     * @param array $joinField   Variable should be have view:
     *     Example:
     *         array(
     *            'alias'     => 'alias_table',
     *            'attribute' => 'table_name', //table name, must be used path of table like 'module/table_name'
     *            'field'     => 'field_name', //selected field name (optional)
     *            //bind main condition
     *            //left field use for joined table
     *            //and right field use for main table of collection
     *            //NOTE: around '=' cannot be used ' ' (space) because on the exploding not use space trimming
     *            'bind'      => 'self_item_id=other_id',
     *            'cond'      => 'alias_table.entity_id = e.entity_id', //additional condition (optional)
     *            'joinType'  => 'LEFT'
     *         )
     *     NOTE: Optional key must be have NULL at least
     */
    public function setJoinField($joinField)
    {
        if (is_array($joinField)) {
            $this->_joinField[] = $joinField;
        }
    }

    /**
     * @return $this
     * @throws Varien_Convert_Exception
     */
    public function load()
    {
        if (!($entityType = $this->getVar('entity_type'))
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Eav_Model_Entity_Interface)
        ) {
            $this->addException(Mage::helper('eav')->__('Invalid entity specified'), Varien_Convert_Exception::FATAL);
        }

        try {
            $collection = $this->_getCollectionForLoad($entityType);

            if (isset($this->_joinAttr) && is_array($this->_joinAttr)) {
                foreach ($this->_joinAttr as $val) {
                    $collection->joinAttribute(
                        $val['alias'],
                        $val['attribute'],
                        $val['bind'],
                        null,
                        strtolower($val['joinType']),
                        $val['storeId'],
                    );
                }
            }

            $filterQuery = $this->getFilter();
            if (is_array($filterQuery)) {
                foreach ($filterQuery as $val) {
                    $collection->addFieldToFilter([$val]);
                }
            }

            $joinFields = $this->_joinField;
            if (isset($joinFields) && is_array($joinFields)) {
                foreach ($joinFields as $field) {
                    $collection->joinField(
                        $field['alias'],
                        $field['attribute'],
                        $field['field'],
                        $field['bind'],
                        $field['cond'],
                        $field['joinType'],
                    );
                }
            }

            /**
             * Load collection ids
             */
            $entityIds = $collection->getAllIds();

            $message = Mage::helper('eav')->__('Loaded %d records', count($entityIds));
            $this->addException($message);
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $message = Mage::helper('eav')->__('Problem loading the collection, aborting. Error: %s', $e->getMessage());
            $this->addException($message, Varien_Convert_Exception::FATAL);
        }

        /**
         * Set collection ids
         */
        $this->setData($entityIds);
        return $this;
    }

    /**
     * Retrieve collection for load
     *
     * @param string $entityType
     * @return Mage_Eav_Model_Entity_Collection|false
     */
    protected function _getCollectionForLoad($entityType)
    {
        return Mage::getResourceModel($entityType . '_collection');
    }

    /**
     * @return $this
     * @throws Varien_Convert_Exception
     */
    public function save()
    {
        $collection = $this->getData();
        if ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $this->addException(Mage::helper('eav')->__('Entity collections expected.'), Varien_Convert_Exception::FATAL);
        }

        $this->addException($collection->getSize() . ' records found.');

        if (!$collection instanceof Mage_Eav_Model_Entity_Collection_Abstract) {
            $this->addException(Mage::helper('eav')->__('Entity collection expected.'), Varien_Convert_Exception::FATAL);
        }

        try {
            $i = 0;
            foreach ($collection->getIterator() as $model) {
                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $model->save();
                $i++;
            }

            $this->addException(Mage::helper('eav')->__('Saved %d record(s).', $i));
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->addException(
                Mage::helper('eav')->__('Problem saving the collection, aborting. Error: %s', $e->getMessage()),
                Varien_Convert_Exception::FATAL,
            );
        }

        return $this;
    }
}
