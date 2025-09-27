<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

/**
 * Class Mage_Reports_Model_Report
 *
 * @package    Mage_Reports
 *
 * @method int getPageSize()
 * @method $this setPageSize(int $value)
 * @method array getStoreIds()
 * @method $this setStoreIds( $value)
 * @method $this setDateRange(string $from, string $to)
 */
class Mage_Reports_Model_Report extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Reports_Model_Report
     */
    protected $_reportModel;

    /**
     * @param string $modelClass
     * @return $this
     */
    public function initCollection($modelClass)
    {
        $this->_reportModel = Mage::getResourceModel($modelClass);

        return $this;
    }

    /**
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Report
     */
    public function getReportFull($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize(false)
            ->setStoreIds($this->getStoreIds());
    }

    /**
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Report
     */
    public function getReport($from, $to)
    {
        return $this->_reportModel
            ->setDateRange($from, $to)
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds());
    }
}
