<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Reports
 */

use Carbon\Carbon;

/**
 * Reports summary collection
 *
 * @package    Mage_Reports
 */
class Mage_Reports_Model_Resource_Entity_Summary_Collection_Abstract extends Varien_Data_Collection
{
    /**
     * Entity collection for summaries
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_entityCollection;

    /**
     * Filters the summaries by some period
     *
     * @param string $periodType
     * @param string|int|null $customStart
     * @param string|int|null $customEnd
     * @return $this
     */
    public function setSelectPeriod($periodType, $customStart = null, $customEnd = null)
    {
        switch ($periodType) {
            case '24h':
                $customStart = Carbon::now()->subDay()->getTimestamp();
                $customEnd   = Carbon::now()->getTimestamp();
                break;

            case '7d':
                $customStart = Carbon::now()->subWeek()->getTimestamp();
                $customEnd   = Carbon::now()->getTimestamp();
                break;

            case '30d':
                $customStart = Carbon::now()->subMonth()->getTimestamp();
                $customEnd   = Carbon::now()->getTimestamp();
                break;

            case '1y':
                $customStart = Carbon::now()->subYear()->getTimestamp();
                $customEnd   = Carbon::now()->getTimestamp();
                break;

            default:
                if (is_string($customStart)) {
                    $customStart = strtotime($customStart);
                }
                if (is_string($customEnd)) {
                    $customEnd = strtotime($customEnd);
                }
                break;
        }

        return $this;
    }

    /**
     * Set date period
     *
     * @param int $period
     * @return $this
     */
    public function setDatePeriod($period)
    {
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        return $this;
    }

    /**
     * Return collection for summaries
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getCollection()
    {
        if (empty($this->_entityCollection)) {
            $this->_initCollection();
        }
        return $this->_entityCollection;
    }

    /**
     * Init collection
     *
     * @return $this
     */
    protected function _initCollection()
    {
        return $this;
    }
}
