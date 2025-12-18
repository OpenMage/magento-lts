<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Composer\InstalledVersions;

/**
 * Data helper for dashboard
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Dashboard_Data extends Mage_Core_Helper_Data
{
    /**
     * Location of the "Enable Chart" config param
     */
    public const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';

    public const XML_PATH_CHART_TYPE    = 'admin/dashboard/chart_type';

    protected $_moduleName = 'Mage_Adminhtml';

    protected $_locale = null;

    protected $_stores = null;

    public function isChartEnabled(): bool
    {
        if (!InstalledVersions::isInstalled('nnnick/chartjs')) {
            return false;
        }

        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE_CHARTS);
    }

    /**
     * Retrieve stores configured in system.
     *
     * @return Mage_Core_Model_Resource_Store_Collection
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getStores()
    {
        if (!$this->_stores) {
            $this->_stores = Mage::app()->getStore()->getResourceCollection()->load();
        }

        return $this->_stores;
    }

    /**
     * Retrieve number of loaded stores
     *
     * @return int
     */
    public function countStores()
    {
        return count($this->getStores()->getItems());
    }

    /**
     * Prepare array with periods for dashboard graphs
     *
     * @return string[]
     */
    public function getDatePeriods()
    {
        return [
            Mage_Reports_Helper_Data::PERIOD_24_HOURS   => $this->__('Last 24 Hours'),
            Mage_Reports_Helper_Data::PERIOD_7_DAYS     => $this->__('Last 7 Days'),
            Mage_Reports_Helper_Data::PERIOD_1_MONTH    => $this->__('Current Month'),
            Mage_Reports_Helper_Data::PERIOD_3_MONTHS   => $this->__('Last 3 Months'),
            Mage_Reports_Helper_Data::PERIOD_6_MONTHS   => $this->__('Last 6 Months'),
            Mage_Reports_Helper_Data::PERIOD_1_YEAR     => $this->__('YTD'),
            Mage_Reports_Helper_Data::PERIOD_2_YEARS    => $this->__('2YTD'),
        ];
    }

    /**
     * Create data hash to ensure that we got valid
     * data, and it is not changed by someone else.
     *
     * @param string $data
     * @return string
     * @deprecated
     */
    public function getChartDataHash($data)
    {
        $secret = (string) Mage::getConfig()->getNode(Mage_Core_Model_App::XML_PATH_INSTALL_DATE);
        return md5($data . $secret);
    }

    public function getChartType(): string
    {
        return Mage::getStoreConfig(self::XML_PATH_CHART_TYPE);
    }
}
