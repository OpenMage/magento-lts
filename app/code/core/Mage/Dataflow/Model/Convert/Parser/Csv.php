<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert csv parser
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    protected $_fields;

    protected $_mapfields = [];

    /**
     * @return $this
     * @throws Throwable
     */
    public function parse()
    {
        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode() . '.UTF-8');

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        if ($fDel == '\t') {
            $fDel = "\t";
        }

        $adapterName   = $this->getVar('adapter', null);
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        } catch (Exception $e) {
            $message = Mage::helper('dataflow')
                ->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!method_exists($adapter, $adapterMethod)) {
            $message = Mage::helper('dataflow')
                ->__('Method "%s" not defined in adapter %s.', $adapterMethod, $adapterName);
            $message = Mage::helper('dataflow')->escapeHtml($message);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        $batchModel = $this->getBatchModel();
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();

        if (Mage::app()->getRequest()->getParam('files')) {
            $file = Mage::app()->getConfig()->getTempVarDir() . '/import/'
                . str_replace('../', '', urldecode(Mage::app()->getRequest()->getParam('files')));
            $this->_copy($file);
        }

        $batchIoAdapter->open(false);

        $isFieldNames = $this->getVar('fieldnames', '') == 'true';
        if (!$isFieldNames && is_array($this->getVar('map'))) {
            $fieldNames = $this->getVar('map');
        } else {
            $fieldNames = [];
            foreach ($batchIoAdapter->read(true, $fDel, $fEnc) as $v) {
                $fieldNames[$v] = $v;
            }
        }

        $countRows = 0;
        while (($csvData = $batchIoAdapter->read(true, $fDel, $fEnc)) !== false) {
            if (count($csvData) == 1 && $csvData[0] === null) {
                continue;
            }

            $itemData = [];
            $countRows++;
            $i = 0;
            foreach ($fieldNames as $field) {
                $itemData[$field] = $csvData[$i] ?? null;
                $i++;
            }

            $batchImportModel = $this->getBatchImportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($itemData)
                ->setStatus(1)
                ->save();
        }

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $countRows));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        //$adapter->$adapterMethod();

        return $this;
    }

    public function parseRow($i, $line)
    {
        if (count($line) === 1) {
            return false;
        }

        if ($i == 0) {
            if ($this->getVar('fieldnames')) {
                $this->_fields = $line;
                return;
            } else {
                foreach ($line as $j => $f) {
                    $this->_fields[$j] = $this->_mapfields[$j];
                }
            }
        }

        $resultRow = [];

        foreach ($this->_fields as $j => $f) {
            $resultRow[$f] = $line[$j] ?? '';
        }
        return $resultRow;
    }

    /**
     * Read data collection and write to temporary file
     *
     * @return $this
     */
    public function unparse()
    {
        $batchExport = $this->getBatchExportModel()
            ->setBatchId($this->getBatchModel()->getId());
        $fieldList = $this->getBatchModel()->getFieldList();
        $batchExportIds = $batchExport->getIdCollection();

        $io = $this->getBatchModel()->getIoAdapter();
        $io->open();

        if (!$batchExportIds) {
            $io->write("");
            $io->close();
            return $this;
        }

        if ($this->getVar('fieldnames')) {
            $csvData = $this->getCsvString($fieldList);
            $io->write($csvData);
        }

        foreach ($batchExportIds as $batchExportId) {
            $csvData = [];
            $batchExport->load($batchExportId);
            $row = $batchExport->getBatchData();

            foreach ($fieldList as $field) {
                $csvData[] = $row[$field] ?? '';
            }
            $csvData = $this->getCsvString($csvData);
            $io->write($csvData);
        }

        $io->close();

        return $this;
    }

    /**
     * @param array $args
     * @return string
     */
    public function unparseRow($args)
    {
        $i = $args['i'];
        $row = $args['row'];

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');
        $lDel = "\r\n";

        if ($fDel == '\t') {
            $fDel = "\t";
        }

        $line = [];
        foreach ($this->_fields as $f) {
            $v = isset($row[$f]) ? str_replace(['"', '\\'], [$fEnc . '"', $fEsc . '\\'], $row[$f]) : '';
            $line[] = $fEnc . $v . $fEnc;
        }

        return implode($fDel, $line);
    }

    /**
     * Retrieve csv string from array
     *
     * @param array $fields
     * @return string
     */
    public function getCsvString($fields = [])
    {
        $delimiter  = $this->getVar('delimiter', ',');
        $enclosure  = $this->getVar('enclose', '');
        $escapeChar = $this->getVar('escape', '\\');

        if ($delimiter == '\t') {
            $delimiter = "\t";
        }

        $str = '';
        foreach ($fields as $value) {
            $escapedValue = Mage::helper("core")->getEscapedCSVData([$value]);
            $value = $escapedValue[0];

            if (str_contains($value, $delimiter) ||
                empty($enclosure) ||
                str_contains($value, $enclosure) ||
                strpos($value, "\n") !== false ||
                strpos($value, "\r") !== false ||
                strpos($value, "\t") !== false ||
                strpos($value, ' ') !== false
            ) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($i = 0; $i < $len; $i++) {
                    if ($value[$i] == $escapeChar) {
                        $escaped = 1;
                    } elseif (!$escaped && $value[$i] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }
                    $str2 .= $value[$i];
                }
                $str2 .= $enclosure;
                $str .= $str2 . $delimiter;
            } else {
                $str .= $enclosure . $value . $enclosure . $delimiter;
            }
        }
        return substr($str, 0, -1) . "\n";
    }
}
