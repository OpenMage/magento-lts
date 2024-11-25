<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Sales_Model_Quote_Address_Total
 *
 * @category   Mage
 * @package    Mage_Sales
 *
 * @method Mage_Sales_Model_Quote_Address getAddress()
 * @method $this setAddress(Mage_Sales_Model_Quote_Address $value)
 * @method string getCode()
 * @method $this setTitle(string $value)
 */
class Mage_Sales_Model_Quote_Address_Total extends Varien_Object
{
    /**
     * Merge numeric total values
     *
     * @return $this
     */
    public function merge(Mage_Sales_Model_Quote_Address_Total $total)
    {
        $newData = $total->getData();
        foreach ($newData as $key => $value) {
            if (is_numeric($value)) {
                $this->setData($key, $this->_getData($key) + $value);
            }
        }
        return $this;
    }
}
