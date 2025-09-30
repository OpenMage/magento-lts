<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert column mapper
 *
 * @package    Varien_Convert
 */
class Varien_Convert_Mapper_Column extends Varien_Convert_Mapper_Abstract
{
    public function map()
    {
        $data = $this->getData();
        $this->validateDataGrid($data);
        if ($this->getVars() && is_array($this->getVars())) {
            $attributesToSelect = $this->getVars();
        } else {
            $attributesToSelect = [];
        }
        $onlySpecified = (bool) $this->getVar('_only_specified') === true;
        $mappedData = [];
        foreach ($data as $i => $row) {
            $newRow = [];
            foreach ($row as $field => $value) {
                if (!$onlySpecified || $onlySpecified && isset($attributesToSelect[$field])) {
                    $newRow[$this->getVar($field, $field)] = $value;
                }
            }
            $mappedData[$i] = $newRow;
        }
        $this->setData($mappedData);
        return $this;
    }
}
