<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */

/**
 * Base class for configure totals order
 *
 * @package    Mage_Sales
 *
 * @method $this setCode(string $value)
 * @method $this setTotalConfigNode(Mage_Core_Model_Config_Element $value)
 */
abstract class Mage_Sales_Model_Order_Total_Abstract extends Varien_Object
{
    /**
     * Process model configuration array.
     * This method can be used for changing models apply sort order
     *
     * @param   array $config
     * @return  array
     */
    public function processConfigArray($config)
    {
        return $config;
    }
}
