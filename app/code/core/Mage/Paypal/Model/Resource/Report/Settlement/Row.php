<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 *Report settlement row resource model
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Row extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/settlement_report_row', 'row_id');
    }
}
