<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Checkout
 */

/**
 * Shopping cart api
 *
 * @category   Mage
 * @package    Mage_Checkout
 */
class Mage_Checkout_Model_Cart_Payment_Api_V2 extends Mage_Checkout_Model_Cart_Payment_Api
{
    /**
      * @param object $data
      * @return array
      */
    protected function _preparePaymentData($data)
    {
        if (($_data = get_object_vars($data)) !== null) {
            return parent::_preparePaymentData($_data);
        }

        return [];
    }
}
