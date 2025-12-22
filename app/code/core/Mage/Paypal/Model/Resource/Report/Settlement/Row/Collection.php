<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Resource collection for report rows
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Row_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('paypal/report_settlement_row');
    }

    /**
     * Join reports info table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->join(
                ['report' => $this->getTable('paypal/settlement_report')],
                'report.report_id = main_table.report_id',
                ['report.account_id', 'report.report_date'],
            );
        return $this;
    }

    /**
     * Filter items collection by account ID
     *
     * @param  string $accountId
     * @return $this
     */
    public function addAccountFilter($accountId)
    {
        $this->getSelect()->where('report.account_id = ?', $accountId);
        return $this;
    }
}
