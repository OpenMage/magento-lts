<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert csv parser
 *
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

        $adapterName   = $this->getVar('adapter');
        $adapterMethod = $this->getVar('method', 'saveRow');

        if (!$adapterName || !$adapterMethod) {
            $message = Mage::helper('dataflow')->__('Please declare "adapter" and "method" nodes first.');
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        try {
            $adapter = Mage::getModel($adapterName);
        } catch (Exception) {
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
            foreach ($batchIoAdapter->read(true, $fDel, $fEnc) as $value) {
                $fieldNames[$value] = $value;
            }
        }

        $countRows = 0;
        while (($csvData = $batchIoAdapter->read(true, $fDel, $fEnc)) !== false) {
            if (count($csvData) == 1 && $csvData[0] === null) {
                continue;
            }

            $itemData = [];
            $countRows++;
            $index = 0;
            foreach ($fieldNames as $field) {
                $itemData[$field] = $csvData[$index] ?? null;
                $index++;
            }

            $this->getBatchImportModel()
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
            }

            foreach ($line as $column => $field) {
                $this->_fields[$column] = $this->_mapfields[$column];
            }
        }

        $resultRow = [];

        foreach ($this->_fields as $column => $field) {
            $resultRow[$field] = $line[$column] ?? '';
        }

        return $resultRow;
    }

    /**
     * Read data collection and write to temporary file
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function unparse()
    {
        $batchExport = $this->getBatchExportModel()
            ->setBatchId($this->getBatchModel()->getId());
        $fieldList = $this->getBatchModel()->getFieldList();
        $batchExportIds = $batchExport->getIdCollection();

        $ioAdapter = $this->getBatchModel()->getIoAdapter();
        $ioAdapter->open();

        if (!$batchExportIds) {
            $ioAdapter->write('');
            $ioAdapter->close();
            return $this;
        }

        if ($this->getVar('fieldnames')) {
            $csvData = $this->getCsvString($fieldList);
            $ioAdapter->write($csvData);
        }

        foreach ($batchExportIds as $batchExportId) {
            $csvData = [];
            $batchExport->load($batchExportId);
            $row = $batchExport->getBatchData();

            foreach ($fieldList as $field) {
                $csvData[] = $row[$field] ?? '';
            }

            $csvData = $this->getCsvString($csvData);
            $ioAdapter->write($csvData);
        }

        $ioAdapter->close();

        return $this;
    }

    /**
     * @param  array  $args
     * @return string
     */
    public function unparseRow($args)
    {
        $row = $args['row'];

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');

        if ($fDel == '\t') {
            $fDel = "\t";
        }

        $line = [];
        foreach ($this->_fields as $field) {
            $value = isset($row[$field]) ? str_replace(['"', '\\'], [$fEnc . '"', $fEsc . '\\'], $row[$field]) : '';
            $line[] = $fEnc . $value . $fEnc;
        }

        return implode($fDel, $line);
    }

    /**
     * Retrieve csv string from array
     *
     * @param  array  $fields
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
            $escapedValue = Mage::helper('core')->getEscapedCSVData([$value]);
            $value = $escapedValue[0];

            if (str_contains($value, $delimiter)
                || empty($enclosure)
                || str_contains($value, $enclosure)
                || str_contains($value, "\n")
                || str_contains($value, "\r")
                || str_contains($value, "\t")
                || str_contains($value, ' ')
            ) {
                $str2 = $enclosure;
                $escaped = 0;
                $len = strlen($value);
                for ($index = 0; $index < $len; $index++) {
                    if ($value[$index] == $escapeChar) {
                        $escaped = 1;
                    } elseif (!$escaped && $value[$index] == $enclosure) {
                        $str2 .= $enclosure;
                    } else {
                        $escaped = 0;
                    }

                    $str2 .= $value[$index];
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
