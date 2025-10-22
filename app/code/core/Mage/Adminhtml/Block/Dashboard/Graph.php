<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @copyright  Copyright (c) 2024 Maho (https://mahocommerce.com)
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard google chart block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Dashboard_Graph extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    public const AXIS_X = 'x';

    public const AXIS_Y = 'y';

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
     * Chart width
     *
     * @var string
     */
    protected $_width = '998px';

    /**
     * Chart height
     *
     * @var string
     */
    protected $_height = '350px';

    /**
     * Html identifier
     *
     * @var string
     */
    protected $_htmlId = '';

    protected $_max;

    protected $_min;

    protected string $dataDelimiter = ',';

    protected string $dataSetdelimiter = '|';

    protected string $dataMissing = '_';

    /**
     * Initialize object
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('dashboard/graph.phtml');
        $this->setDataHelperName('adminhtml/dashboard_order');

        /** @var Mage_Adminhtml_Helper_Dashboard_Order $dataHelper */
        $dataHelper = $this->getDataHelper();

        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $this->getRequest();

        $dataHelper->setParam('store', $request->getParam('store'));
        $dataHelper->setParam('website', $request->getParam('website'));
        $dataHelper->setParam('group', $request->getParam('group'));
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
        $this->_dataRows = (array) $rows;
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
     * @deprecated
     */
    public function getChartUrl($directUrl = true)
    {
        return '';
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     * @throws Zend_Date_Exception
     */
    public function generateChart(): array
    {
        $params = [];

        $this->_allSeries = $this->getRowsData($this->_dataRows);

        foreach ($this->_axisMaps as $axis => $attr) {
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        [$datas, $dates] = $this->getChartDatasAndDates();
        $this->_axisLabels['x'] = $dates;
        $this->_allSeries = $datas;

        // Image-Charts Awesome data format values
        $params['chd'] = 'a:';
        $params['chd'] .= $this->getChartDataFromAllSeries();

        $valueBuffer = [];

        if (!array_key_exists(self::AXIS_X, $this->_axisLabels) ||
            !array_key_exists(self::AXIS_Y, $this->_axisLabels)
        ) {
            return $params;
        }

        $params['chxt'] = implode(',', array_keys($this->_axisLabels));

        $indexid = 0;
        foreach (array_keys($this->_axisLabels) as $idx) {
            if ($idx === self::AXIS_X) {
                foreach ($this->_axisLabels[$idx] as $_index => $_label) {
                    $this->_axisLabels[$idx][$_index] = '';
                    switch ($this->getDataHelper()->getParam('period')) {
                        case Mage_Reports_Helper_Data::PERIOD_24_HOURS:
                            $this->_axisLabels[$idx][$_index] = $this->formatTime(
                                new Zend_Date($_label, 'yyyy-MM-dd HH:00'),
                                'short',
                            );
                            break;
                        case Mage_Reports_Helper_Data::PERIOD_7_DAYS:
                        case Mage_Reports_Helper_Data::PERIOD_1_MONTH:
                        case Mage_Reports_Helper_Data::PERIOD_3_MONTHS:
                        case Mage_Reports_Helper_Data::PERIOD_6_MONTHS:
                            $this->_axisLabels[$idx][$_index] = $this->formatDate(
                                new Zend_Date($_label, 'yyyy-MM-dd'),
                            );
                            break;
                        case Mage_Reports_Helper_Data::PERIOD_1_YEAR:
                        case Mage_Reports_Helper_Data::PERIOD_2_YEARS:
                            $formats = Mage::app()->getLocale()->getTranslationList('datetime');
                            $format = $formats['yyMM'] ?? 'MM/yyyy';
                            $format = str_replace(['yyyy', 'yy', 'MM'], ['Y', 'y', 'm'], $format);
                            $this->_axisLabels[$idx][$_index] = date($format, strtotime($_label));
                            break;
                    }
                }

                $tmpstring      = implode('|', $this->_axisLabels[$idx]);
                $valueBuffer[]  = $indexid . ':|' . $tmpstring;
            }

            if ($idx === self::AXIS_Y) {
                $valueBuffer[] = $indexid . ':|' . implode('|', $this->getChartYLabels());
            }

            $indexid++;
        }

        $params['chxl'] = implode('|', $valueBuffer);

        return $params;
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     */
    private function getChartDatasAndDates(): array
    {
        $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        [$dateStart, $dateEnd] = Mage::getResourceModel('reports/order_collection')
            ->getDateRange($this->getDataHelper()->getParam('period'), '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);

        $date  = '';
        $dates = [];
        $datas = [];
        $period = $this->getDataHelper()->getParam('period');

        while ($dateStart->compare($dateEnd) < 0) {
            switch ($period) {
                case Mage_Reports_Helper_Data::PERIOD_24_HOURS:
                    $date = $dateStart->toString('yyyy-MM-dd HH:00');
                    $dateStart->addHour(1);
                    break;
                case Mage_Reports_Helper_Data::PERIOD_7_DAYS:
                case Mage_Reports_Helper_Data::PERIOD_1_MONTH:
                    $date = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case Mage_Reports_Helper_Data::PERIOD_3_MONTHS:
                case Mage_Reports_Helper_Data::PERIOD_6_MONTHS:
                    $date = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addWeek(1);
                    break;
                case Mage_Reports_Helper_Data::PERIOD_1_YEAR:
                case Mage_Reports_Helper_Data::PERIOD_2_YEARS:
                    $date = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
            }

            if (in_array($period, [
                Mage_Reports_Helper_Data::PERIOD_3_MONTHS,
                Mage_Reports_Helper_Data::PERIOD_6_MONTHS,
            ])) {
                $axisTimestamps = [];
                foreach ($this->_axisLabels['x'] as $axisDate) {
                    $axisTimestamps[] = (new Zend_Date($axisDate, 'yyyy-MM-dd'))->getTimestamp();
                }
            }

            foreach (array_keys($this->getAllSeries()) as $index) {
                if (isset($axisTimestamps)) {
                    $dateObj = new Zend_Date($date, 'yyyy-MM-dd');
                    $weekStartTs = $dateObj->getTimestamp();
                    $weekEndTs = $dateObj->addWeek(1)->getTimestamp();

                    $found = false;
                    foreach ($axisTimestamps as $axisTs) {
                        if ($axisTs >= $weekStartTs && $axisTs < $weekEndTs) {
                            $datas[$index][] = (float) array_shift($this->_allSeries[$index]);
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $datas[$index][] = 0;
                    }
                } else {
                    $datas[$index][] = in_array($date, $this->_axisLabels['x'])
                        ? (float) array_shift($this->_allSeries[$index])
                        : 0;
                }
            }

            $dates[] = $date;
        }

        return [$datas, $dates];
    }

    private function getChartDataFromAllSeries(): string
    {
        $yorigin = 0;
        $chartdata = [];

        foreach ($this->getAllSeries() as $serie) {
            $thisdataarray = $serie;
            $thisdataarrayCount = count($thisdataarray);
            for ($j = 0; $j < $thisdataarrayCount; $j++) {
                $currentvalue = $thisdataarray[$j];
                if (is_numeric($currentvalue)) {
                    $ylocation = $yorigin + $currentvalue;
                    $chartdata[] = $ylocation . $this->dataDelimiter;
                } else {
                    $chartdata[] = $this->dataMissing . $this->dataDelimiter;
                }
            }

            $chartdata[] = $this->dataSetdelimiter;
        }

        $buffer = implode('', $chartdata);
        $buffer = rtrim($buffer, $this->dataSetdelimiter);
        $buffer = rtrim($buffer, $this->dataDelimiter);

        return str_replace(($this->dataDelimiter . $this->dataDelimiter), $this->dataSetdelimiter, $buffer);
    }

    private function getChartYLabels(): array
    {
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

        return $yLabels;
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
                foreach ((array) $attributes as $attr) {
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
            $number /= 10;
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

        $this->setChartId($this->getHtmlId() . random_int(0, 100));
        $this->getDataHelper()->setParam(
            'period',
            ($period && in_array($period, $availablePeriods)) ? $period : '24h',
        );
    }

    public function getChartData(): string
    {
        return json_encode($this->_allSeries[array_key_first($this->_allSeries)]);
    }

    public function getChartLabels(): string
    {
        return json_encode($this->_axisLabels['x']);
    }

    public function getChartType(): string
    {
        /** @var Mage_Adminhtml_Helper_Dashboard_Data $helper */
        $helper = $this->helper('adminhtml/dashboard_data');
        return $helper->getChartType();
    }

    public function getChartId(): string
    {
        return $this->getDataByKey('chart_id');
    }

    public function setChartId(string $chartId): self
    {
        return $this->setData('chart_id', $chartId);
    }

    public function getChartBackgroundColor(): string
    {
        return 'rgba(113,121,142,0.7)';
    }

    public function getChartHoverBackgroundColor(): string
    {
        return 'rgba(113,121,142,1.0)';
    }
}
