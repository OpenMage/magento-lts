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
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Convert_Adapter_Entity extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    /**
     * Current store model
     *
     * @var Mage_Core_Model_Store
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
            if (substr($key, 0, 6) === 'filter') {
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
                    if (strpos($k, $key . '/') === 0) {
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
                       'joinType' => $joinType
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
                        'eq'        => $val
                    ];
                    break;
                case 'like':
                    $attr = [
                        'attribute' => $keyDB,
                        'like'      => '%' . $val . '%'
                    ];
                    break;
                case 'startsWith':
                     $attr = [
                         'attribute' => $keyDB,
                         'like'      => $val . '%'
                     ];
                    break;
                case 'fromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'],
                        'to'        => $val['to']
                    ];
                    break;
                case 'dateFromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'],
                        'to'        => $val['to'],
                        'date'      => true
                    ];
                    break;
                case 'datetimeFromTo':
                    $attr = [
                        'attribute' => $keyDB,
                        'from'      => $val['from'] ?? null,
                        'to'        => $val['to'] ?? null,
                        'datetime'  => true
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
     * @param string $joinAttr
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
            || !(Mage::getResourceSingleton($entityType) instanceof Mage_Eav_Model_Entity_Interface)) {
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
                        $val['storeId']
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
                        $field['joinType']
                    );
                }
            }

            /**
             * Load collection ids
             */
            $entityIds = $collection->getAllIds();

            $message = Mage::helper('eav')->__("Loaded %d records", count($entityIds));
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
     * @return Mage_Eav_Model_Entity_Collection
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
                $model->save();
                $i++;
            }
            $this->addException(Mage::helper('eav')->__("Saved %d record(s).", $i));
        } catch (Varien_Convert_Exception $e) {
            throw $e;
        } catch (Exception $e) {
            $this->addException(
                Mage::helper('eav')->__('Problem saving the collection, aborting. Error: %s', $e->getMessage()),
                Varien_Convert_Exception::FATAL
            );
        }
        return $this;
    }
}
