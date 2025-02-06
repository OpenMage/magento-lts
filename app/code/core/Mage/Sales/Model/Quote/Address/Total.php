<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
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
