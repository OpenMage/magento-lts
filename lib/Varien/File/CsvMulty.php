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
 * @package     Varien_File
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Csv parse
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Varien/File/Csv.php';

class Varien_File_Csv_Multy extends Varien_File_Csv {
    /**
     * Retrieve CSV file data as pairs with duplicates
     *
     * @param   string $file
     * @param   int $keyIndex
     * @param   int $valueIndex
     * @return  array
     */
    public function getDataPairs($file, $keyIndex=0, $valueIndex=1)
    {
        $data = array();
        $csvData = $this->getData($file);
        $line_number = 0;
        foreach ($csvData as $rowData) {
            $line_number++;
            if (isset($rowData[$keyIndex])) {
                if(isset($data[$rowData[$keyIndex]])){
                    if(isset($data[$rowData[$keyIndex]]['duplicate'])){
                        #array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $line_number,'value' => isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null));
                        $data[$rowData[$keyIndex]]['duplicate']['line'] .=', '.$line_number;
                    } else {
                        $tmp_value = $data[$rowData[$keyIndex]]['value'];
                        $tmp_line  = $data[$rowData[$keyIndex]]['line'];
                        $data[$rowData[$keyIndex]]['duplicate'] = array();
                        #array_push($data[$rowData[$keyIndex]]['duplicate'],array('line' => $tmp_line.' ,'.$line_number,'value' => $tmp_value));
                        $data[$rowData[$keyIndex]]['duplicate']['line'] = $tmp_line.' ,'.$line_number;
                        $data[$rowData[$keyIndex]]['duplicate']['value'] = $tmp_value;
                    }
                } else {
                    $data[$rowData[$keyIndex]] = array();
                    $data[$rowData[$keyIndex]]['line'] = $line_number;
                    $data[$rowData[$keyIndex]]['value'] = isset($rowData[$valueIndex]) ? $rowData[$valueIndex] : null;
                }
            }
        }
        return $data;
    }

}
?>
