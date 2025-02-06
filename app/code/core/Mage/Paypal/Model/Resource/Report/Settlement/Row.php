<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 *Report settlement row resource model
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Resource_Report_Settlement_Row extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('paypal/settlement_report_row', 'row_id');
    }
}
