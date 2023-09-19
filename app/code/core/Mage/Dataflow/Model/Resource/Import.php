<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DataFlow Import resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Resource_Import extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/import', 'import_id');
    }

    /**
     * Returns all import data select by session id
     *
     * @param int $sessionId
     * @return Varien_Db_Select
     */
    public function select($sessionId)
    {
        return $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('session_id=?', $sessionId)
            ->where('status=?', 0);
    }

    /**
     * Load all import data by session id
     *
     * @param int $sessionId
     * @param int $min
     * @param int $max
     * @return array
     */
    public function loadBySessionId($sessionId, $min = 0, $max = 100)
    {
        if (!is_numeric($min) || !is_numeric($max)) {
            return [];
        }
        $bind = [
            'status'     => 0,
            'session_id' => $sessionId,
            'min_id'     => (int)$min,
            'max_id'     => (int)$max,
        ];
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('dataflow/import'))
            ->where('import_id >= :min_id')
            ->where('import_id >= :max_id')
            ->where('status= :status')
            ->where('session_id = :session_id');
        return $read->fetchAll($select, $bind);
    }

    /**
     * Load total import data by session id
     *
     * @param int $sessionId
     * @return array
     */
    public function loadTotalBySessionId($sessionId)
    {
        $bind = [
            'status'    => 0,
            'session_id' => $sessionId
        ];
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(
                $this->getTable('dataflow/import'),
                ['max' => 'max(import_id)', 'min' => 'min(import_id)', 'cnt' => 'count(*)']
            )
            ->where('status = :status')
            ->where('session_id = :$session_id');
        return $read->fetchRow($select, $bind);
    }

    /**
     * Load import data by id
     *
     * @param int $importId
     * @return array
     */
    public function loadById($importId)
    {
        $bind = [
            'status'    => 0,
            'import_id' => $importId
        ];
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('dataflow/import'))
            ->where('status = :status')
            ->where('import_id = :import_id');
        return $read->fetchRow($select, $bind);
    }
}
