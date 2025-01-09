<?php

/**
 * @category   Mage
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
