<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Report settlement resource model
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Resource_Report_Settlement extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Table name
     *
     * @var string
     */
    protected $_rowsTable;

    /**
     * Init main table
     *
     */
    protected function _construct()
    {
        $this->_init('paypal/settlement_report', 'report_id');
        $this->_rowsTable = $this->getTable('paypal/settlement_report_row');
    }

    /**
     * Save report rows collected in settlement model
     *
     * @param Mage_Paypal_Model_Report_Settlement $object
     * @return $this
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $rows = $object->getRows();
        if (is_array($rows)) {
            $adapter  = $this->_getWriteAdapter();
            $reportId = (int) $object->getId();
            $adapter->beginTransaction();
            try {
                if ($reportId) {
                    $adapter->delete($this->_rowsTable, ['report_id = ?' => $reportId]);
                }

                /** @var Mage_Core_Model_Date $date */
                $date = Mage::getSingleton('core/date');

                foreach (array_keys($rows) as $key) {
                    /*
                     * Converting dates
                     */
                    $completionDate = new Zend_Date($rows[$key]['transaction_completion_date']);
                    $rows[$key]['transaction_completion_date'] = $date->date(null, $completionDate->getTimestamp());
                    $initiationDate = new Zend_Date($rows[$key]['transaction_initiation_date']);
                    $rows[$key]['transaction_initiation_date'] = $date->date(null, $initiationDate->getTimestamp());
                    /*
                     * Converting numeric
                     */
                    $rows[$key]['fee_amount'] = (float) $rows[$key]['fee_amount'];
                    /*
                     * Setting reportId
                     */
                    $rows[$key]['report_id'] = $reportId;
                }

                if (!empty($rows)) {
                    $adapter->insertMultiple($this->_rowsTable, $rows);
                }

                $adapter->commit();
            } catch (Exception) {
                $adapter->rollBack();
            }
        }

        return $this;
    }

    /**
     * Check if report with same account and report date already fetched
     *
     * @param string $accountId
     * @param string $reportDate
     * @return $this
     */
    public function loadByAccountAndDate(Mage_Paypal_Model_Report_Settlement $report, $accountId, $reportDate)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getMainTable())
            ->where('account_id = :account_id')
            ->where('report_date = :report_date');

        $data = $adapter->fetchRow($select, [':account_id' => $accountId, ':report_date' => $reportDate]);
        if ($data) {
            $report->addData($data);
        }

        return $this;
    }
}
