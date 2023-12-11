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
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert excel xml parser
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Parser_Xml_Excel extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    /**
     * Simple Xml object
     *
     * @var SimpleXMLElement|null
     */
    protected $_xmlElement;

    /**
     * Field list
     *
     * @var array|null
     */
    protected $_parseFieldNames;

    public function parse()
    {
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
            $message = Mage::helper('dataflow')->__('Declared adapter %s was not found.', $adapterName);
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::FATAL);
            return $this;
        }

        if (!method_exists($adapter, $adapterMethod)) {
            $message = Mage::helper('dataflow')
                ->__('Method "%s" was not defined in adapter %s.', $adapterMethod, $adapterName);
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
            $this->_parseFieldNames = $this->getVar('map');
        }

        $worksheet = $this->getVar('single_sheet', '');

        $xmlString = $xmlRowString = '';
        $countRows = 0;
        $isWorksheet = $isRow = false;
        while (($xmlOriginalString = $batchIoAdapter->read()) !== false) {
            $xmlString .= $xmlOriginalString;
            if (!$isWorksheet) {
                $strposS = strpos($xmlString, '<Worksheet');
                $substrL = 10;
                //fix for OpenOffice
                if ($strposS === false) {
                    $strposS = strpos($xmlString, '<ss:Worksheet');
                    $substrL = 13;
                }
                if ($strposS === false) {
                    $xmlString = substr($xmlString, -13);
                    continue;
                }

                $xmlTmpString = substr($xmlString, $strposS);
                $strposF = strpos($xmlTmpString, '>');

                if ($strposF === false) {
                    $xmlString = $xmlTmpString;
                    continue;
                }

                if (!$worksheet) {
                    $xmlString = substr($xmlTmpString, $strposF);
                    $isWorksheet = true;
                    continue;
                } else {
                    if (preg_match('/ss:Name=\"' . preg_quote($worksheet, '/') . '\"/siU', substr($xmlTmpString, 0, $strposF))) {
                        $xmlString = substr($xmlTmpString, $strposF);
                        $isWorksheet = true;
                        continue;
                    } else {
                        $xmlString = '';
                        continue;
                    }
                }
            } else {
                $xmlString = $this->_parseXmlRow($xmlString);

                $strposS = strpos($xmlString, '</Worksheet>');
                $substrL = 12;
                //fix for OpenOffice
                if ($strposS === false) {
                    $strposS = strpos($xmlString, '</ss:Worksheet>');
                    $substrL = 15;
                }
                if ($strposS !== false) {
                    $xmlString = substr($xmlString, $strposS + $substrL);
                    $isWorksheet = false;

                    continue;
                }
            }
        }

        $this->addException(Mage::helper('dataflow')->__('Found %d rows.', $this->_countRows));
        $this->addException(Mage::helper('dataflow')->__('Starting %s :: %s', $adapterName, $adapterMethod));

        $batchModel->setParams($this->getVars())
            ->setAdapter($adapterName)
            ->save();

        return $this;
    }

    /**
     * Parse MS Excel XML string
     *
     * @param string $xmlString
     * @return string
     */
    protected function _parseXmlRow($xmlString)
    {
        $found = true;
        while ($found === true) {
            $strposS = strpos($xmlString, '<Row');

            if ($strposS === false) {
                $found = false;
                continue;
            }

            $xmlTmpString = substr($xmlString, $strposS);
            $strposF = strpos($xmlTmpString, '</Row>');

            if ($strposF !== false) {
                $xmlRowString = substr($xmlTmpString, 0, $strposF + 6);

                $this->_saveParsedRow($xmlRowString);

                $xmlString = substr($xmlTmpString, $strposF + 6);
            } else {
                $found = false;
                continue;
            }
        }

        return $xmlString;
    }

    protected function _saveParsedRow($xmlString)
    {
        $xml = '<' . '?xml version="1.0"?' . '><' . '?mso-application progid="Excel.Sheet"?'
            . '><Workbook'
            . ' xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:html="http://www.w3.org/TR/REC-html40"'
            . ' xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">'
            . $xmlString
            . '</Workbook>';

        try {
            $xmlElement = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $message = 'Invalid XML row';
            $this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
            return $this;
        }

        $xmlData  = [];
        $itemData = [];
        $cellIndex = 0;
        foreach ($xmlElement->Row->children() as $cell) {
            if (is_null($this->_parseFieldNames)) {
                $xmlData[(string)$cell->Data] = (string)$cell->Data;
            } else {
                $attributes = $cell->attributes('urn:schemas-microsoft-com:office:spreadsheet');
                if ($attributes && isset($attributes['Index'])) {
                    $cellIndex = $attributes['Index'] - 1;
                }
                $xmlData[$cellIndex] = (string)$cell->Data;
                $cellIndex++;
            }
        }

        if (is_null($this->_parseFieldNames)) {
            $this->_parseFieldNames = $xmlData;
            return $this;
        }

        $this->_countRows ++;

        $i = 0;
        foreach ($this->_parseFieldNames as $field) {
            $itemData[$field] = $xmlData[$i] ?? null;
            $i++;
        }

        $batchImportModel = $this->getBatchImportModel()
            ->setId(null)
            ->setBatchId($this->getBatchModel()->getId())
            ->setBatchData($itemData)
            ->setStatus(1)
            ->save();

        return $this;
    }

    public function unparse()
    {
        $batchExport = $this->getBatchExportModel()
            ->setBatchId($this->getBatchModel()->getId());
        $fieldList = $this->getBatchModel()->getFieldList();
        $batchExportIds = $batchExport->getIdCollection();

        if (!is_array($batchExportIds)) {
            return $this;
        }

        $io = $this->getBatchModel()->getIoAdapter();
        $io->open();

        $xml = '<' . '?xml version="1.0"?' . '><' . '?mso-application progid="Excel.Sheet"?'
            . '><Workbook'
            . ' xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:html="http://www.w3.org/TR/REC-html40"'
            . ' xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">'
            . '<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">'
            . '</OfficeDocumentSettings>'
            . '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">'
            . '</ExcelWorkbook>';
        $io->write($xml);

        $wsName = htmlspecialchars($this->getVar('single_sheet'));
        $wsName = !empty($wsName) ? $wsName : Mage::helper('dataflow')->__('Sheet 1');

        $xml = '<Worksheet ss:Name="' . $wsName . '"><Table>';
        $io->write($xml);

        if ($this->getVar('fieldnames')) {
            $xml = $this->_getXmlString($fieldList);
            $io->write($xml);
        }

        foreach ($batchExportIds as $batchExportId) {
            $xmlData = [];
            $batchExport->load($batchExportId);
            $row = $batchExport->getBatchData();

            foreach ($fieldList as $field) {
                $xmlData[] = $row[$field] ?? '';
            }
            $xmlData = $this->_getXmlString($xmlData);
            $io->write($xmlData);
        }

        $xml = '</Table></Worksheet></Workbook>';
        $io->write($xml);
        $io->close();

        return $this;
    }

    /**
     * Prepare and return XML string for MS Excel XML from array
     *
     * @param array $fields
     * @return string
     */
    protected function _getXmlString(array $fields = [])
    {
        $xmlHeader = '<?xml version="1.0"?>' . "\n";
        $xmlRegexp = '/^<cell><row>(.*)?<\/row><\/cell>\s?$/ms';

        if (is_null($this->_xmlElement)) {
            $xmlString = $xmlHeader . '<cell><row></row></cell>';
            $this->_xmlElement = new SimpleXMLElement($xmlString, LIBXML_NOBLANKS);
        }

        $xmlData = [];
        $xmlData[] = '<Row>';
        foreach ($fields as $value) {
            $this->_xmlElement->row = htmlspecialchars($value);
            $value = str_replace($xmlHeader, '', $this->_xmlElement->asXML());
            $value = preg_replace($xmlRegexp, '\\1', $value);
            if (is_numeric($value)) {
                $value = trim($value);
                $dataType = 'Number';
            } else {
                $dataType = 'String';
            }
            $value = str_replace(["\r\n", "\r", "\n"], '&#10;', $value);

            $xmlData[] = '<Cell><Data ss:Type="' . $dataType . '">' . $value . '</Data></Cell>';
        }
        $xmlData[] = '</Row>';

        return implode('', $xmlData);
    }
}
