<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_File
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Csv parse
 *
 */

require_once 'Varien/File/Csv.php';

class Varien_File_Csv_Multy extends Varien_File_Csv
{
    /**
     * Retrieve CSV file data as pairs with duplicates
     *
     * @param   string $file
     * @param   int $keyIndex
     * @param   int $valueIndex
     * @return  array
     */
    public function getDataPairs($file, $keyIndex = 0, $valueIndex = 1)
    {
        $data = [];
        $csvData = $this->getData($file);
        $line_number = 0;
        foreach ($csvData as $rowData) {
            $line_number++;
            if (isset($rowData[$keyIndex])) {
                if (isset($data[$rowData[$keyIndex]])) {
                    if (isset($data[$rowData[$keyIndex]]['duplicate'])) {
                        #array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $line_number,'value' => isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null));
                        $data[$rowData[$keyIndex]]['duplicate']['line'] .= ', ' . $line_number;
                    } else {
                        $tmp_value = $data[$rowData[$keyIndex]]['value'];
                        $tmp_line  = $data[$rowData[$keyIndex]]['line'];
                        $data[$rowData[$keyIndex]]['duplicate'] = [];
                        #array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $tmp_line.' ,'.$line_number,'value' => $tmp_value));
                        $data[$rowData[$keyIndex]]['duplicate']['line'] = $tmp_line . ' ,' . $line_number;
                        $data[$rowData[$keyIndex]]['duplicate']['value'] = $tmp_value;
                    }
                } else {
                    $data[$rowData[$keyIndex]] = [];
                    $data[$rowData[$keyIndex]]['line'] = $line_number;
                    $data[$rowData[$keyIndex]]['value'] = $rowData[$valueIndex] ?? null;
                }
            }
        }
        return $data;
    }
}
