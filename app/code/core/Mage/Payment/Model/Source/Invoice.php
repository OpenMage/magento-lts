<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Automatic invoice create source model
 *
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
