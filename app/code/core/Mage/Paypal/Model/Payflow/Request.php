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
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payflow Link request model
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
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
     */
    public function __call($method, $args)
    {
        $key = $this->_underscore(substr($method,3));
        if (isset($args[0]) && (strstr($args[0], '=') || strstr($args[0], '&'))) {
            $key .= '[' . strlen($args[0]) . ']';
        }
        switch (substr($method, 0, 3)) {
            case 'get' :
                return $this->getData($key, isset($args[0]) ? $args[0] : null);

            case 'set' :
                return $this->setData($key, isset($args[0]) ? $args[0] : null);

            case 'uns' :
                return $this->unsetData($key);

            case 'has' :
                return isset($this->_data[$key]);
        }
        throw new Varien_Exception("Invalid method ".get_class($this)."::".$method."(".print_r($args,1).")");
    }
}
