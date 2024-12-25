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
 * Convert column mapper
 *
 * @category   Varien
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
