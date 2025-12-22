<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert excel xml parser
 *
 * @package    Varien_Convert
 */
class Varien_Convert_Parser_Xml_Excel extends Varien_Convert_Parser_Abstract
{
    /**
     * XML instance for a cell data
     *
     * @var null|SimpleXMLElement
     */
    protected $_xmlElement;

    public function parse()
    {
        $this->validateDataString();

        $dom = new DOMDocument();
        $dom->loadXML($this->getData());

        $worksheets = $dom->getElementsByTagName('Worksheet');
        /** @var DOMElement $worksheet */
        foreach ($worksheets as $worksheet) {
            $wsName = $worksheet->getAttribute('ss:Name');
            $rows = $worksheet->getElementsByTagName('Row');
            $firstRow = true;
            $fieldNames = [];
            $wsData = [];
            /** @var DOMElement $row */
            foreach ($rows as $row) {
                $index = 1;
                $cells = $row->getElementsByTagName('Cell');
                $rowData = [];
                /** @var DOMElement $cell */
                foreach ($cells as $cell) {
                    $value = $cell->getElementsByTagName('Data')->item(0)->nodeValue;
                    $ind = $cell->getAttribute('ss:Index');
                    if (!is_null($ind) && $ind > 0) {
                        $index = $ind;
                    }

                    if ($firstRow && !$this->getVar('fieldnames')) {
                        $fieldNames[$index] = 'column' . $index;
                    }

                    if ($firstRow && $this->getVar('fieldnames')) {
                        $fieldNames[$index] = $value;
                    } else {
                        $rowData[$fieldNames[$index]] = $value;
                    }

                    $index++;
                }

                $firstRow = false;
                if (!empty($rowData)) {
                    $wsData[] = $rowData;
                }
            }

            $data[$wsName] = $wsData;
            $this->addException('Found worksheet "' . $wsName . '" with ' . count($wsData) . ' row(s)');
        }

        if ($wsName = $this->getVar('single_sheet')) {
            if (isset($data[$wsName])) {
                $data = $data[$wsName];
            } else {
                reset($data);
                $data = current($data);
            }
        }

        $this->setData($data);
        return $this;
    }

    public function unparse()
    {
        if ($wsName = $this->getVar('single_sheet')) {
            $data = [$wsName => $this->getData()];
        } else {
            $data = $this->getData();
        }

        $this->validateDataGrid();

        $xml = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?' . '>
<Workbook xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';

        if (is_array($data)) {
            foreach ($data as $wsName => $wsData) {
                if (!is_array($wsData)) {
                    continue;
                }

                $fields = $this->getGridFields($wsData);

                $xml .= '<Worksheet ss:Name="' . $wsName . '"><ss:Table>';
                if ($this->getVar('fieldnames')) {
                    $xml .= '<ss:Row>';
                    foreach ($fields as $fieldName) {
                        $xml .= '<ss:Cell><Data ss:Type="String">' . $fieldName . '</Data></ss:Cell>';
                    }

                    $xml .= '</ss:Row>';
                }

                foreach ($wsData as $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $xml .= '<ss:Row>';
                    foreach ($fields as $fieldName) {
                        $data = $row[$fieldName] ?? '';
                        $fieldType = is_numeric($data) ? 'Number' : 'String';
                        $xml .= '<ss:Cell><Data ss:Type="' . $fieldType . '">' . $data . '</Data></ss:Cell>';
                    }

                    $xml .= '</ss:Row>';
                }

                $xml .= '</ss:Table></Worksheet>';
            }
        }

        $xml .= '</Workbook>';

        $this->setData($xml);

        return $this;
    }

    /**
     * Retrieve Excel 2003 XML Document header XML fragment
     *
     * @param  string $sheetName the Worksheet name
     * @return string
     */
    public function getHeaderXml($sheetName = '')
    {
        if (empty($sheetName)) {
            $sheetName = 'Sheet 1';
        }

        $sheetName = htmlspecialchars($sheetName);
        return '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook'
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
            . '</ExcelWorkbook>'
            . '<Worksheet ss:Name="' . $sheetName . '">'
            . '<Table>';
    }

    /**
     * Retrieve Excel 2003 XML Document footer XML fragment
     *
     * @return string
     */
    public function getFooterXml()
    {
        return '</Table></Worksheet></Workbook>';
    }

    /**
     * Convert an array to Excel 2003 XML Document a Row XML fragment
     *
     * @return string
     */
    public function getRowXml(array $row)
    {
        $xmlHeader = '<?xml version="1.0"?>' . "\n";
        $xmlRegexp = '/^<cell><row>(.*)?<\/row><\/cell>\s?$/ms';

        if (is_null($this->_xmlElement)) {
            $xmlString = $xmlHeader . '<cell><row></row></cell>';
            $this->_xmlElement = new SimpleXMLElement($xmlString, LIBXML_NOBLANKS);
        }

        $xmlData = [];
        $xmlData[] = '<Row>';
        foreach ($row as $value) {
            $this->_xmlElement->row = htmlspecialchars((string) $value);
            $value = str_replace($xmlHeader, '', $this->_xmlElement->asXML());
            $value = preg_replace($xmlRegexp, '\\1', $value);
            $dataType = 'String';
            if (is_numeric($value)) {
                $dataType = 'Number';
                // is_numeric(' 96000') returns true, but Excel argues about space
                $value = trim($value);
            }

            $value = str_replace("\r\n", '&#10;', $value);
            $value = str_replace("\r", '&#10;', $value);
            $value = str_replace("\n", '&#10;', $value);

            $xmlData[] = '<Cell><Data ss:Type="' . $dataType . '">' . $value . '</Data></Cell>';
        }

        $xmlData[] = '</Row>';

        return implode('', $xmlData);
    }
}
