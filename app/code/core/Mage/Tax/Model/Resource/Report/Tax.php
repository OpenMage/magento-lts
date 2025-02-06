<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Tax report resource model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Resource_Report_Tax extends Mage_Reports_Model_Resource_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        Mage::getResourceModel('tax/report_tax_createdat')->aggregate($from, $to);
        Mage::getResourceModel('tax/report_tax_updatedat')->aggregate($from, $to);
        $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE);

        return $this;
    }
}
