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
 * @method int   getPageSize()
 * @method array getStoreIds()
 * @method $this setDateRange(string $dateFrom, string $dateTo)
 * @method $this setPageSize(int $value)
 * @method $this setStoreIds( $value)
 */
class Mage_Reports_Model_Report extends Mage_Core_Model_Abstract
{
    /**
     * @var Mage_Reports_Model_Report
     */
    protected $_reportModel;

    /**
     * @param  string $modelClass
     * @return $this
     */
    public function initCollection($modelClass)
    {
        /** @var Mage_Reports_Model_Report $model */
        $model = Mage::getResourceModel($modelClass);
        $this->_reportModel = $model;

        return $this;
    }

    /**
     * @param  null|string               $dateFrom
     * @param  null|string               $dateTo
     * @return Mage_Reports_Model_Report
     */
    public function getReportFull($dateFrom, $dateTo)
    {
        return $this->_reportModel
            ->setDateRange($dateFrom, $dateTo)
            ->setPageSize(false)
            ->setStoreIds($this->getStoreIds());
    }

    /**
     * @param  null|string               $dateFrom
     * @param  null|string               $dateTo
     * @return Mage_Reports_Model_Report
     */
    public function getReport($dateFrom, $dateTo)
    {
        return $this->_reportModel
            ->setDateRange($dateFrom, $dateTo)
            ->setPageSize($this->getPageSize())
            ->setStoreIds($this->getStoreIds());
    }
}
