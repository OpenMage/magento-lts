<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_Shipping_Model_Rate_Result_Error
 *
 * @method $this setCarrier(string $value)
 * @method $this setCarrierTitle(string $value)
 * @method $this setErrorMessage(string $value)
 */
class Mage_Shipping_Model_Rate_Result_Error extends Mage_Shipping_Model_Rate_Result_Abstract
{
    /**
     * @return string
     */
    public function getErrorMessage()
    {
        if (!$this->getData('error_message')) {
            $this->setData('error_message', Mage::helper('shipping')->__('This shipping method is currently unavailable. If you would like to ship using this shipping method, please contact us.'));
        }
        return $this->getData('error_message');
    }
}
