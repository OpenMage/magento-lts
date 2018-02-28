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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Convert
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Convert csv parser
 *
 * @category   Varien
 * @package    Varien_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Convert_Parser_Csv extends Varien_Convert_Parser_Abstract
{
    public function parse()
    {
        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

        $fp = tmpfile();
        fputs($fp, $this->getData());
        fseek($fp, 0);

        $data = array();
        for ($i=0; $line = fgetcsv($fp, 4096, $fDel, $fEnc); $i++) {
            if (0==$i) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach ($line as $j=>$f) {
                        $fields[$j] = 'column'.($j+1);
                    }
                }
            }
            $row = array();
            foreach ($fields as $j=>$f) {
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

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        // fixed for multibyte characters
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

        $fp = tmpfile();
        fputs($fp, $this->getData());
        fseek($fp, 0);

        $data = array();
        $sessionId = Mage::registry('current_dataflow_session_id');
        $import = Mage::getModel('dataflow/import');
        $map = new Varien_Convert_Mapper_Column();
        for ($i=0; $line = fgetcsv($fp, 4096, $fDel, $fEnc); $i++) {
            if (0==$i) {
                if ($this->getVar('fieldnames')) {
                    $fields = $line;
                    continue;
                } else {
                    foreach ($line as $j=>$f) {
                        $fields[$j] = 'column'.($j+1);
                    }
                }
            }
            $row = array();
            foreach ($fields as $j=>$f) {
                $row[$f] = $line[$j];
            }
            $map->setData(array($row));
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
    } // end

    public function unparse()
    {
        $csv = '';

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        $fEsc = $this->getVar('escape', '\\');
        $lDel = "\r\n";

        if ($fDel=='\\t') {
            $fDel = "\t";
        }

        $data = $this->getData();
        $fields = $this->getGridFields($data);
        $lines = array();

        if ($this->getVar('fieldnames')) {
            $line = array();
            foreach ($fields as $f) {
                $line[] = $fEnc.str_replace(array('"', '\\'), array($fEsc.'"', $fEsc.'\\'), $f).$fEnc;
            }
            $lines[] = join($fDel, $line);
        }
        foreach ($data as $i=>$row) {
            $line = array();
            foreach ($fields as $f) {
                /*
                if (isset($row[$f]) && (preg_match('\"', $row[$f]) || preg_match('\\', $row[$f]))) {
                    $tmp = str_replace('\\', '\\\\',$row[$f]);
                    echo str_replace('"', '\"',$tmp).'<br>';
                }
                */
                $v = isset($row[$f]) ? str_replace(array('"', '\\'), array($fEnc.'"', $fEsc.'\\'), $row[$f]) : '';

                $line[] = $fEnc.$v.$fEnc;
            }
            $lines[] = join($fDel, $line);
        }
        $result = join($lDel, $lines);
        $this->setData($result);

        return $this;
    }
}
