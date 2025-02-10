<?php
/**
 * Base class for configure totals order
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Sales
 * @method $this setCode(string $value)
 * @method $this setTotalConfigNode(array $value)
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
