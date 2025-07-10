<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * Payflow Link request model
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Payflow_Request extends Varien_Object
{
    /**
     * Set/Get attribute wrapper
     * Also add length path if key contains = or &
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     * @SuppressWarnings("PHPMD.DevelopmentCodeFragment")
     */
    public function __call($method, $args)
    {
        $key = $this->_underscore(substr($method, 3));
        if (isset($args[0]) && (strstr($args[0], '=') || strstr($args[0], '&'))) {
            $key .= '[' . strlen($args[0]) . ']';
        }
        return match (substr($method, 0, 3)) {
            'get' => $this->getData($key, $args[0] ?? null),
            'set' => $this->setData($key, $args[0] ?? null),
            'uns' => $this->unsetData($key),
            'has' => isset($this->_data[$key]),
            default => throw new Varien_Exception('Invalid method ' . static::class . '::' . $method . '(' . print_r($args, true) . ')'),
        };
    }
}
