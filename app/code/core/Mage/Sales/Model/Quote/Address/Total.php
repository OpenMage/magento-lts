<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Sales_Model_Quote_Address_Total
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
     * @param Mage_Sales_Model_Quote_Address_Total $total
     * @return $this
     */
    public function merge(Mage_Sales_Model_Quote_Address_Total $total)
    {
        $newData = $total->getData();
        foreach ($newData as $key => $value) {
            if (is_numeric($value)) {
                $this->setData($key, $this->_getData($key)+$value);
            }
        }
        return $this;
    }
}
