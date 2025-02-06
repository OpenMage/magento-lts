<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Automatic invoice create source model
 *
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Model_Source_Invoice
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('core')->__('Yes'),
            ],
            [
                'value' => '',
                'label' => Mage::helper('core')->__('No'),
            ],
        ];
    }
}
