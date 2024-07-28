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
 * @copyright  Copyright (c) 2021-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert csv parser
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Session_Parser_Csv extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    public function parse()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');

        if ($fDel == '\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode() . '.UTF-8');

        $fp = tmpfile();
        fwrite($fp, $this->getData());
        fseek($fp, 0);

        $data = [];
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getModel('dataflow/import');
        $map = new Varien_Convert_Mapper_Column();
        for ($i = 0; $line = fgetcsv($fp, 4096, $fDel, $fEnc); $i++) {
            if ($i == 0) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach ($line as $j => $f) {
                        $fields[$j] = 'column' . ($j + 1);
                    }
                }
            }
            $row = [];
            foreach ($fields as $j => $f) {
                $row[$f] = $line[$j];
            }
            /*
            if ($i <= 100)
            {
                $data[] = $row;
            }
            */
            //$map = new Varien_Convert_Mapper_Column();
            $map->setData([$row]);
            $map->map();
            $row = $map->getData();
            //$import = Mage::getModel('dataflow/import');
            $import->setImportId(0);
            $import->setSessionId($sessionId);
            $import->setSerialNumber($i);
            $import->setValue(serialize($row[0]));
            $import->save();
            //unset($import);
        }
        fclose($fp);
        unset($sessionId);
        //$this->setData($data);
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
                $v = isset($row[$f]) ? str_replace(['"', '\\'], [$fEsc . '"', $fEsc . '\\'], $row[$f]) : '';

                $line[] = $fEnc . $v . $fEnc;
            }
            $lines[] = implode($fDel, $line);
        }
        $result = implode($lDel, $lines);
        $this->setData($result);

        return $this;
    }
}
