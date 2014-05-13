<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Chart data xml renderer block abstract
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphDataAbstract
    extends Mage_Adminhtml_Block_Dashboard_Graph
{
    /**
     * X axis param
     */
    const AXIS_X = 'x';

    /**
     * Y axis param
     */
    const AXIS_Y = 'y';

    /**
     * Date range param for 24 hours
     */
    const DATE_RANGE_24H = '24h';

    /**
     * Date range param for 7 days
     */
    const DATE_RANGE_7D = '7d';

    /**
     * Date range param for 1 month
     */
    const DATE_RANGE_1M = '1m';

    /**
     * Date range param for 1 year
     */
    const DATE_RANGE_1Y = '1y';

    /**
     * Date range param for 2 years
     */
    const DATE_RANGE_2Y = '2y';

    /**
     * Chart xml object
     *
     * @var Mage_XmlConnect_Model_Simplexml_Element
     */
    protected $_xmlObj;

    /**
     * Add chart data from all stores
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphDataAbstract
     */
    protected function _addAllStoreData()
    {
        $dataValuesXml = $this->getXmlObj()->addCustomChild('chart_data_values');
        $dataDescriptionXml = $this->getXmlObj()->addCustomChild('chart_data_description');

        foreach (Mage::helper('xmlconnect/adminApplication')->getSwitcherList() as $storeFilter) {
            $this->getDataHelper()->setParam('store', $storeFilter ? $storeFilter : null);
            $storeId = $this->getDataHelper()->getParam('store');
            $storeId = $storeId ? $storeId : Mage_XmlConnect_Helper_AdminApplication::ALL_STORE_VIEWS;

            $valuesXml = $dataValuesXml->addCustomChild('values', null, array(
                'store_id' => $storeId
            ));

            $descriptionXml = $dataDescriptionXml->addCustomChild('values', null, array(
                'store_id' => $storeId
            ));

            foreach ($this->getRangeOptions() as $rangeFilter) {
                $this->getDataHelper()->setParam('period', $rangeFilter['value']);
                $this->getDataHelper()->initCollection();
                $chartData = $this->getChartData();
                $valuesXml->addCustomChild('item', $chartData['values'], array(
                    'range_id' => $this->getDataHelper()->getParam('period')
                ));
                $descriptionXml->addCustomChild('item', $chartData['description'], array(
                    'range_id' => $this->getDataHelper()->getParam('period')
                ));
            }
        }
        return $this;
    }

    /**
     * Get chart data array as chart values => chart reference axis description
     *
     * @throws Mage_Core_Exception
     * @return array
     */
    public function getChartData()
    {
        if (!$this->getCount()) {
            return array('values' => '', 'description' => '');
        }

        $this->_allSeries = $this->getRowsData($this->_dataRows);

        foreach ($this->_axisMaps as $axis => $attr) {
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        list($dateSeries, $dataSeries) = $this->_getRangeAndData();

        $this->_axisLabels[self::AXIS_X] = $this->_normalizeDateSeries($dateSeries);
        $this->_allSeries = $dataSeries;

        $valueBuffer = array();
        $params = array();

        if (sizeof($this->_axisLabels) > 0) {
            $indexId = 0;
            foreach ($this->_axisLabels as $idx => $labels) {
                if ($idx == self::AXIS_X) {
                    $valueBuffer[] = $indexId . ":|" . implode('|', $this->_getXLabels());
                } elseif ($idx == self::AXIS_Y) {
                    $valueBuffer[] = $indexId . ":|" . implode('|', $this->_getYLabels());
                }
                $indexId++;
            }
            $params['description'] = implode('|', $valueBuffer);
        };

        foreach ($this->getAllSeries() as $row) {
            array_walk($row, create_function('&$val', '$val = ceil($val);'));
            $params['values'] = implode('|', $row);
        }
        return $params;
    }

    /**
     * Get array of date range and values range series
     *
     * @return array
     */
    protected function _getRangeAndData()
    {
        $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
            ->getDateRange($this->getDataHelper()->getParam('period'), '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);

        $dateSeries = array();
        $dataSeries = array();
        while ($dateStart->compare($dateEnd) < 0) {
            switch ($this->getDataHelper()->getParam('period')) {
                case self::DATE_RANGE_24H:
                    $date = $dateStart->toString('yyyy-MM-dd HH:00');
                    $dateStart->addHour(1);
                    break;
                case self::DATE_RANGE_7D:
                case self::DATE_RANGE_1M:
                    $date = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case self::DATE_RANGE_1Y:
                case self::DATE_RANGE_2Y:
                    $date = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
            }
            foreach ($this->getAllSeries() as $index => $series) {
                if (in_array($date, $this->_axisLabels[self::AXIS_X])) {
                    $dataSeries[$index][] = (float)array_shift($this->_allSeries[$index]);
                } else {
                    $dataSeries[$index][] = 0;
                }
            }
            $dateSeries[] = $date;
        }
        return array($dateSeries, $dataSeries);
    }

    /**
     * Skip excess values of date series
     *
     * Keep count of date series up to 15 items
     *
     * @param array $dateSeries
     * @return array
     */
    protected function _normalizeDateSeries($dateSeries)
    {
        /**
         * setting skip step
         */
        if (count($dateSeries) > 8 && count($dateSeries) < 15) {
            $skipStep = 1;
        } elseif (count($dateSeries) >= 15) {
            $skipStep = 2;
        } else {
            $skipStep = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i = 0;
        foreach ($dateSeries as $index => $date) {
            if ($i == $skipStep) {
                $dateSeries[$index] = $date;
                $i = 0;
            } else {
                $dateSeries[$index] = '';
                ++$i;
            }
        }
        return $dateSeries;
    }

    /**
     * Get X axis params array
     *
     * @return array
     */
    protected function _getXLabels()
    {
        /**
         * Format date
         */
        foreach ($this->_axisLabels[self::AXIS_X] as $index => $label) {
            if ($label != '') {
                switch ($this->getDataHelper()->getParam('period')) {
                    case self::DATE_RANGE_24H:
                        $this->_axisLabels[self::AXIS_X][$index] = $this->formatTime(
                            new Zend_Date($label, 'yyyy-MM-dd HH:00'), 'short', false
                        );
                        break;
                    case self::DATE_RANGE_7D:
                    case self::DATE_RANGE_1M:
                        $this->_axisLabels[self::AXIS_X][$index] = $this->formatDate(
                            new Zend_Date($label, 'yyyy-MM-dd')
                        );
                        break;
                    case self::DATE_RANGE_1Y:
                    case self::DATE_RANGE_2Y:
                        $formats = Mage::app()->getLocale()->getTranslationList('datetime');
                        $format = isset($formats['yyMM']) ? $formats['yyMM'] : 'MM/yyyy';
                        $format = str_replace(array("yyyy", "yy", "MM"), array("Y", "y", "m"), $format);
                        $this->_axisLabels[self::AXIS_X][$index] = date($format, strtotime($label));
                        break;
                    default:
                        Mage::throwException($this->__('Range param doesn\'t recognized'));
                        break;
                }
            } else {
                $this->_axisLabels[self::AXIS_X][$index] = '';
            }
        }
        return $this->_axisLabels[self::AXIS_X];
    }

    /**
     * Get Y axis params array
     *
     * @return array
     */
    protected function _getYLabels()
    {
        $localMaxValue = $localMinValue = array();
        // process each string in the array, and find the max length
        foreach ($this->getAllSeries() as $index => $series) {
            $localMaxValue[$index] = max($series);
            $localMinValue[$index] = min($series);
        }

        if (is_numeric($this->_max)) {
            $maxvalue = $this->_max;
        } else {
            $maxvalue = max($localMaxValue);
        }
        if (is_numeric($this->_min)) {
            $minvalue = $this->_min;
        } else {
            $minvalue = min($localMinValue);
        }

        // default values
        $yLabels = array();

        if ($minvalue >= 0 && $maxvalue >= 0) {
            $miny = 0;
            if ($maxvalue > 10) {
                $p = pow(10, $this->_getPow($maxvalue));
                $maxy = ceil($maxvalue / $p) * $p;
                $yLabels = range($miny, $maxy, $p);
            } else {
                $maxy = ceil($maxvalue + 1);
                $yLabels = range($miny, $maxy, 1);
            }
        }
        return $yLabels;
    }

    /**
     * Get chart xml object
     *
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getXmlObj()
    {
        return $this->_xmlObj;
    }

    /**
     * Set chart xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Block_Adminhtml_Connect_Dashboard_GraphDataAbstract
     */
    public function setXmlObj($xmlObj)
    {
        $this->_xmlObj = $xmlObj;
        return $this;
    }
}
