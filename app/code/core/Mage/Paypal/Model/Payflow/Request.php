<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Payflow Link request model
 *
 * @category   Mage
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
        switch (substr($method, 0, 3)) {
            case 'get':
                return $this->getData($key, $args[0] ?? null);

            case 'set':
                return $this->setData($key, $args[0] ?? null);

            case 'uns':
                return $this->unsetData($key);

            case 'has':
                return isset($this->_data[$key]);
        }
        throw new Varien_Exception('Invalid method ' . get_class($this) . '::' . $method . '(' . print_r($args, true) . ')');
    }
}
