<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert csv parser
 *
 * @category   Varien
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

        $fp = tmpfile();
        fputs($fp, $this->getData());
        fseek($fp, 0);

        $data = [];
        for ($i = 0; $line = fgetcsv($fp, 4096, $fDel, $fEnc, $fEsc); $i++) {
            if (0 == $i) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach (array_keys($line) as $j) {
                        $fields[$j] = 'column' . ($j + 1);
                    }
                }
            }
            $row = [];
            foreach ($fields as $j => $f) {
                $row[$f] = $line[$j];
            }
            $data[] = $row;
        }
        fclose($fp);
        $this->setData($data);
        return $this;
    }

    // experimental code
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

        $fp = tmpfile();
        fputs($fp, $this->getData());
        fseek($fp, 0);

        $data = [];
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getModel('dataflow/import');
        $map = new Varien_Convert_Mapper_Column();
        for ($i = 0; $line = fgetcsv($fp, 4096, $fDel, $fEnc, $fEsc); $i++) {
            if (0 == $i) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach (array_keys($line) as $j) {
                        $fields[$j] = 'column' . ($j + 1);
                    }
                }
            }
            $row = [];
            foreach ($fields as $j => $f) {
                $row[$f] = $line[$j];
            }
            $map->setData([$row]);
            $map->map();
            $row = $map->getData();
            $import->setImportId(0);
            $import->setSessionId($sessionId);
            $import->setSerialNumber($i);
            $import->setValue(serialize($row[0]));
            $import->save();
        }
        fclose($fp);
        unset($sessionId);
        return $this;
    }

    public function unparse()
    {
        $csv = '';

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
            foreach ($fields as $f) {
                $line[] = $fEnc . str_replace(['"', '\\'], [$fEsc . '"', $fEsc . '\\'], $f) . $fEnc;
            }
            $lines[] = implode($fDel, $line);
        }
        foreach ($data as $i => $row) {
            $line = [];
            foreach ($fields as $f) {
                /*
                if (isset($row[$f]) && (preg_match('\"', $row[$f]) || preg_match('\\', $row[$f]))) {
                    $tmp = str_replace('\\', '\\\\',$row[$f]);
                    echo str_replace('"', '\"',$tmp).'<br>';
                }
                */
                $v = isset($row[$f]) ? str_replace(['"', '\\'], [$fEnc . '"', $fEsc . '\\'], $row[$f]) : '';

                $line[] = $fEnc . $v . $fEnc;
            }
            $lines[] = implode($fDel, $line);
        }
        $result = implode($lDel, $lines);
        $this->setData($result);

        return $this;
    }
}
