<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * ImportExport import data resource model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Resource_Import_Data extends Mage_Core_Model_Resource_Db_Abstract implements IteratorAggregate
{
    /**
     * @var null|IteratorIterator
     */
    protected $_iterator = null;

    protected function _construct()
    {
        $this->_init('importexport/importdata', 'id');
    }

    /**
     * Retrieve an external iterator
     *
     * @return IteratorIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['data'])
            ->order('id ASC');
        $stmt = $adapter->query($select);

        $stmt->setFetchMode(Zend_Db::FETCH_NUM);
        if ($stmt instanceof IteratorAggregate) {
            $iterator = $stmt->getIterator();
        } else {
            // Statement doesn't support iterating, so fetch all records and create iterator ourself
            $rows = $stmt->fetchAll();
            $iterator = new ArrayIterator($rows);
        }

        return $iterator;
    }

    /**
     * Clean all bunches from table.
     *
     * @return int
     */
    public function cleanBunches()
    {
        return $this->_getWriteAdapter()->delete($this->getMainTable());
    }

    /**
     * Return behavior from import data table.
     *
     * @return string
     * @throws Exception
     */
    public function getBehavior()
    {
        $adapter = $this->_getReadAdapter();
        $behaviors = array_unique($adapter->fetchCol(
            $adapter->select()
                ->from($this->getMainTable(), ['behavior']),
        ));
        if (count($behaviors) != 1) {
            Mage::throwException(Mage::helper('importexport')->__('Error in data structure: behaviors are mixed'));
        }

        return $behaviors[0];
    }

    /**
     * Return entity type code from import data table.
     *
     * @return string
     * @throws Exception
     */
    public function getEntityTypeCode()
    {
        $adapter = $this->_getReadAdapter();
        $entityCodes = array_unique($adapter->fetchCol(
            $adapter->select()
                ->from($this->getMainTable(), ['entity']),
        ));
        if (count($entityCodes) != 1) {
            Mage::throwException(Mage::helper('importexport')->__('Error in data structure: entity codes are mixed'));
        }

        return $entityCodes[0];
    }

    /**
     * Get next bunch of validated rows.
     *
     * @return null|array
     */
    public function getNextBunch()
    {
        if ($this->_iterator === null) {
            $this->_iterator = $this->getIterator();
            $this->_iterator->rewind();
        }

        if ($this->_iterator->valid()) {
            $dataRow = $this->_iterator->current();
            $dataRow = Mage::helper('core')->jsonDecode($dataRow[0]);
            $this->_iterator->next();
        } else {
            $this->_iterator = null;
            $dataRow = null;
        }

        return $dataRow;
    }

    /**
     * Save import rows bunch.
     *
     * @param string $entity
     * @param string $behavior
     * @return int
     */
    public function saveBunch($entity, $behavior, array $data)
    {
        return $this->_getWriteAdapter()->insert(
            $this->getMainTable(),
            ['behavior' => $behavior, 'entity' => $entity, 'data' => Mage::helper('core')->jsonEncode($data)],
        );
    }
}
