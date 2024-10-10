<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard google chart block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Graph extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    /**
     * Api URL
     */
    public const API_URL = 'https://image-charts.com/chart';

    /**
     * All series
     *
     * @var array
     */
    protected $_allSeries = [];

    /**
     * Axis labels
     *
     * @var array
     */
    protected $_axisLabels = [];

    /**
     * Axis maps
     *
     * @var array
     */
    protected $_axisMaps = [];

    /**
     * Data rows
     *
     * @var array
     */
    protected $_dataRows = [];

    /**
     * Simple encoding chars
     *
     * @var string
     */
    protected $_simpleEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    /**
     * Extended encoding chars
     *
     * @var string
     */
    protected $_extendedEncoding = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';

    /**
     * Chart width
     *
     * @var string
     */
    protected $_width = '587';

    /**
     * Chart height
     *
     * @var string
     */
    protected $_height = '300';

    /**
     * Google chart api data encoding
     *
     * @deprecated since the Google Image Charts API not accessible from March 14, 2019
     *
     * @var string
     */
    protected $_encoding = 'e';

    /**
     * Html identifier
     *
     * @var string
     */
    protected $_htmlId = '';

    protected $_max;
    protected $_min;

    /**
     * Initialize object
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/graph.phtml');
    }

    /**
     * Get tab template
     *
     * @return string
     */
    protected function _getTabTemplate()
    {
        return 'dashboard/graph.phtml';
    }

    /**
     * Set data rows
     *
     * @param mixed $rows
     */
    public function setDataRows($rows)
    {
        $this->_dataRows = (array)$rows;
    }

    /**
     * Add series
     *
     * @param string $seriesId
     */
    public function addSeries($seriesId, array $options)
    {
        $this->_allSeries[$seriesId] = $options;
    }

    /**
     * Get series
     *
     * @param string $seriesId
     * @return mixed
     */
    public function getSeries($seriesId)
    {
        return $this->_allSeries[$seriesId] ?? false;
    }

    /**
     * Get all series
     *
     * @return array
     */
    public function getAllSeries()
    {
        return $this->_allSeries;
    }

    /**
     * Get chart url
     *
     * @param bool $directUrl
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Date_Exception
     */
    public function getChartUrl($directUrl = true)
    {
        $params = [
            'cht'  => 'lc',
            'chf'  => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
            'chm'  => 'B,f4d4b2,0,0,0',
            'chco' => 'db4814',
            'chxs' => '0,0,11|1,0,11',
            'chma' => '15,15,15,15'
        ];

        $this->_allSeries = $this->getRowsData($this->_dataRows);

        foreach ($this->_axisMaps as $axis => $attr) {
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        list($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
            ->getDateRange($this->getDataHelper()->getParam('period'), '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);

        $d = '';
        $dates = [];
        $datas = [];

        while ($dateStart->compare($dateEnd) < 0) {
            switch ($this->getDataHelper()->getParam('period')) {
                case '24h':
                    $d = $dateStart->toString('yyyy-MM-dd HH:00');
                    $dateStart->addHour(1);
                    break;
                case '7d':
                case '1m':
                    $d = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case '1y':
                case '2y':
                    $d = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
            }
            foreach (array_keys($this->getAllSeries()) as $index) {
                if (in_array($d, $this->_axisLabels['x'])) {
                    $datas[$index][] = (float)array_shift($this->_allSeries[$index]);
                } else {
                    $datas[$index][] = 0;
                }
            }
            $dates[] = $d;
        }

        /**
         * setting skip step
         */
        if (count($dates) > 8 && count($dates) < 15) {
            $c = 1;
        } elseif (count($dates) >= 15) {
            $c = 2;
        } else {
            $c = 0;
        }
        /**
         * skipping some x labels for good reading
         */
        $i = 0;
        foreach ($dates as $k => $d) {
            if ($i == $c) {
                $dates[$k] = $d;
                $i = 0;
            } else {
                $dates[$k] = '';
                $i++;
            }
        }

        $this->_axisLabels['x'] = $dates;
        $this->_allSeries = $datas;

        // Image-Charts Awesome data format values
        $params['chd'] = 'a:';
        $dataDelimiter = ',';
        $dataSetdelimiter = '|';
        $dataMissing = '_';
        $localmaxlength = [];
        $localmaxvalue = [];
        $localminvalue = [];

        // process each string in the array, and find the max length
        foreach ($this->getAllSeries() as $index => $serie) {
            $localmaxlength[$index] = count($serie);
            $localmaxvalue[$index] = max($serie);
            $localminvalue[$index] = min($serie);
        }

        if (is_numeric($this->_max)) {
            $maxvalue = $this->_max;
        } else {
            $maxvalue = max($localmaxvalue);
        }
        if (is_numeric($this->_min)) {
            $minvalue = $this->_min;
        } else {
            $minvalue = min($localminvalue);
        }

        // default values
        $yLabels = [];
        $miny = 0;
        $maxy = 0;
        $yorigin = 0;

        if ($minvalue >= 0 && $maxvalue >= 0) {
            $miny = 0;
            if ($maxvalue > 10) {
                $p = 10 ** $this->_getPow($maxvalue);
                $maxy = (ceil($maxvalue / $p)) * $p;
                $yLabels = range($miny, $maxy, $p);
            } else {
                $maxy = ceil($maxvalue + 1);
                $yLabels = range($miny, $maxy, 1);
            }
        }

        $chartdata = [];

        foreach ($this->getAllSeries() as $serie) {
            $thisdataarray = $serie;
            // phpcs:ignore Ecg.Performance.Loop.ArraySize
            $thisdataarrayCount = count($thisdataarray);
            for ($j = 0; $j < $thisdataarrayCount; $j++) {
                $currentvalue = $thisdataarray[$j];
                if (is_numeric($currentvalue)) {
                    $ylocation = $yorigin + $currentvalue;
                    $chartdata[] = $ylocation . $dataDelimiter;
                } else {
                    $chartdata[] = $dataMissing . $dataDelimiter;
                }
            }
            $chartdata[] = $dataSetdelimiter;
        }
        $buffer = implode('', $chartdata);

        $buffer = rtrim($buffer, $dataSetdelimiter);
        $buffer = rtrim($buffer, $dataDelimiter);
        $buffer = str_replace(($dataDelimiter . $dataSetdelimiter), $dataSetdelimiter, $buffer);

        $params['chd'] .= $buffer;

        $valueBuffer = [];

        if (count($this->_axisLabels)) {
            $params['chxt'] = implode(',', array_keys($this->_axisLabels));
            $indexid = 0;
            foreach (array_keys($this->_axisLabels) as $idx) {
                if ($idx === 'x') {
                    /**
                     * Format date
                     */
                    foreach ($this->_axisLabels[$idx] as $_index => $_label) {
                        if ($_label != '') {
                            switch ($this->getDataHelper()->getParam('period')) {
                                case '24h':
                                    $this->_axisLabels[$idx][$_index] = $this->formatTime(
                                        new Zend_Date($_label, 'yyyy-MM-dd HH:00'),
                                        'short'
                                    );
                                    break;
                                case '7d':
                                case '1m':
                                    $this->_axisLabels[$idx][$_index] = $this->formatDate(
                                        new Zend_Date($_label, 'yyyy-MM-dd')
                                    );
                                    break;
                                case '1y':
                                case '2y':
                                    $formats = Mage::app()->getLocale()->getTranslationList('datetime');
                                    $format = $formats['yyMM'] ?? 'MM/yyyy';
                                    $format = str_replace(['yyyy', 'yy', 'MM'], ['Y', 'y', 'm'], $format);
                                    $this->_axisLabels[$idx][$_index] = date($format, strtotime($_label));
                                    break;
                            }
                        } else {
                            $this->_axisLabels[$idx][$_index] = '';
                        }
                    }

                    $tmpstring = implode('|', $this->_axisLabels[$idx]);

                    $valueBuffer[] = $indexid . ':|' . $tmpstring;
                    // phpcs:ignore Ecg.Performance.Loop.ArraySize
                    if (count($this->_axisLabels[$idx]) > 1) {
                        // phpcs:ignore Ecg.Performance.Loop.ArraySize
                        $deltaX = 100 / (count($this->_axisLabels[$idx]) - 1);
                    } else {
                        $deltaX = 100;
                    }
                } elseif ($idx === 'y') {
                    $valueBuffer[] = $indexid . ':|' . implode('|', $yLabels);
                    // phpcs:ignore Ecg.Performance.Loop.ArraySize
                    if (count($yLabels) - 1) {
                        // phpcs:ignore Ecg.Performance.Loop.ArraySize
                        $deltaY = 100 / (count($yLabels) - 1);
                    } else {
                        $deltaY = 100;
                    }
                }
                $indexid++;
            }
            $params['chxl'] = implode('|', $valueBuffer);
        }

        // chart size
        $params['chs'] = $this->getWidth() . 'x' . $this->getHeight();

        if (isset($deltaX, $deltaY)) {
            $params['chg'] = $deltaX . ',' . $deltaY . ',1,0';
        }

        // return the encoded data
        if ($directUrl) {
            $p = [];
            foreach ($params as $name => $value) {
                $p[] = $name . '=' . urlencode($value);
            }
            return self::API_URL . '?' . implode('&', $p);
        }

        $gaData = urlencode(base64_encode(json_encode($params)));
        $gaHash = Mage::helper('adminhtml/dashboard_data')->getChartDataHash($gaData);
        $params = ['ga' => $gaData, 'h' => $gaHash];
        return $this->getUrl('*/*/tunnel', ['_query' => $params]);
    }

    /**
     * Get rows data
     *
     * @param array $attributes
     * @param bool $single
     * @return array
     */
    protected function getRowsData($attributes, $single = false)
    {
        $items = $this->getCollection()->getItems();
        $options = [];
        foreach ($items as $item) {
            if ($single) {
                $options[] = max(0, $item->getData($attributes));
            } else {
                foreach ((array)$attributes as $attr) {
                    $options[$attr][] = max(0, $item->getData($attr));
                }
            }
        }
        return $options;
    }

    /**
     * Set axis labels
     *
     * @param string $axis
     * @param array $labels
     */
    public function setAxisLabels($axis, $labels)
    {
        $this->_axisLabels[$axis] = $labels;
    }

    /**
     * Set html id
     *
     * @param string $htmlId
     */
    public function setHtmlId($htmlId)
    {
        $this->_htmlId = $htmlId;
    }

    /**
     * Get html id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->_htmlId;
    }

    /**
     * Return pow
     *
     * @param int $number
     * @return int
     */
    protected function _getPow($number)
    {
        $pow = 0;
        while ($number >= 10) {
            $number = $number / 10;
            $pow++;
        }
        return $pow;
    }

    /**
     * Return chart width
     *
     * @return string
     */
    protected function getWidth()
    {
        return $this->_width;
    }

    /**
     * Return chart height
     *
     * @return string
     */
    protected function getHeight()
    {
        return $this->_height;
    }

    /**
     * Prepare chart data
     *
     * @return void
     * @throws Exception
     */
    protected function _prepareData()
    {
        /** @var Mage_Adminhtml_Helper_Dashboard_Data $helper */
        $helper = $this->helper('adminhtml/dashboard_data');
        $availablePeriods = array_keys($helper->getDatePeriods());
        $period = $this->getRequest()->getParam('period');

        $this->getDataHelper()->setParam(
            'period',
            ($period && in_array($period, $availablePeriods)) ? $period : '24h'
        );
    }
}
