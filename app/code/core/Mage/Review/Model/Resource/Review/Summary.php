<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review summary resource model
 *
 * @package    Mage_Review
 */
class Mage_Review_Model_Resource_Review_Summary extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define module
     *
     */
    protected function _construct()
    {
        $this->_init('review/review_aggregate', 'entity_pk_value');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->where('store_id = ?', (int) $object->getStoreId());
        return $select;
    }

    /**
     * Reaggregate all data by rating summary
     *
     * @param array $summary
     * @return $this
     */
    public function reAggregate($summary)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from(
                $this->getMainTable(),
                [
                    'primary_id' => new Zend_Db_Expr('MAX(primary_id)'),
                    'store_id',
                    'entity_pk_value',
                ],
            )
            ->group(['entity_pk_value', 'store_id']);
        foreach ($adapter->fetchAll($select) as $row) {
            if (isset($summary[$row['store_id']]) && isset($summary[$row['store_id']][$row['entity_pk_value']])) {
                $summaryItem = $summary[$row['store_id']][$row['entity_pk_value']];
                if ($summaryItem->getCount()) {
                    $ratingSummary = round($summaryItem->getSum() / $summaryItem->getCount());
                } else {
                    $ratingSummary = $summaryItem->getSum();
                }
            } else {
                $ratingSummary = 0;
            }

            $adapter->update(
                $this->getMainTable(),
                ['rating_summary' => $ratingSummary],
                $adapter->quoteInto('primary_id = ?', $row['primary_id']),
            );
        }

        return $this;
    }
}
