<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert csv parser
 *
 * @package    Varien_Convert
 */
class Varien_Convert_Parser_Csv extends Varien_Convert_Parser_Abstract
{
    public function parse()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');

        if ($fDel == '\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode() . '.UTF-8');

        $stream = tmpfile();
        fwrite($stream, $this->getData());
        fseek($stream, 0);

        $data = [];
        $fields = [];
        for ($index = 0; $line = fgetcsv($stream, 4096, $fDel, $fEnc, $fEsc); $index++) {
            if (0 == $index) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                }

                foreach (array_keys($line) as $column) {
                    $fields[$column] = 'column' . ($column + 1);
                }
            }

            $row = [];
            foreach ($fields as $key => $field) {
                $row[$field] = $line[$key];
            }

            $data[] = $row;
        }

        fclose($stream);
        $this->setData($data);
        return $this;
    }

    // experimental code

    /**
     * @throws Throwable
     */
    public function parseTest()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');

        if ($fDel == '\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode() . '.UTF-8');

        $stream = tmpfile();
        fwrite($stream, $this->getData());
        fseek($stream, 0);
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getModel('dataflow/import');
        $map = new Varien_Convert_Mapper_Column();

        $fields = [];
        for ($index = 0; $line = fgetcsv($stream, 4096, $fDel, $fEnc, $fEsc); $index++) {
            if (0 == $index) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                }

                foreach (array_keys($line) as $column) {
                    $fields[$column] = 'column' . ($column + 1);
                }
            }

            $row = [];
            foreach ($fields as $key => $field) {
                $row[$field] = $line[$key];
            }

            $map->setData([$row]);
            $map->map();
            $row = $map->getData();
            $import->setImportId(0);
            $import->setSessionId($sessionId);
            $import->setSerialNumber($index);
            $import->setValue(serialize($row[0]));
            $import->save();
        }

        fclose($stream);
        unset($sessionId);
        return $this;
    }

    public function unparse()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');
        $lDel = "\r\n";

        if ($fDel == '\\t') {
            $fDel = "\t";
        }

        $data = $this->getData();
        $fields = $this->getGridFields($data);
        $lines = [];

        if ($this->getVar('fieldnames')) {
            $line = [];
            foreach ($fields as $field) {
                $line[] = $fEnc . str_replace(['"', '\\'], [$fEsc . '"', $fEsc . '\\'], $field) . $fEnc;
            }

            $lines[] = implode($fDel, $line);
        }

        foreach ($data as $row) {
            $line = [];
            foreach ($fields as $field) {
                $value = isset($row[$field]) ? str_replace(['"', '\\'], [$fEnc . '"', $fEsc . '\\'], $row[$field]) : '';
                $line[] = $fEnc . $value . $fEnc;
            }

            $lines[] = implode($fDel, $line);
        }

        $result = implode($lDel, $lines);
        $this->setData($result);

        return $this;
    }
}
