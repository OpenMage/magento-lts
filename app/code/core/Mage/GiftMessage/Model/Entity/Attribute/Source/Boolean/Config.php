<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 */

/**
 * Gift Message attribute source model
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @deprecated after 1.4.2.0
 */
class Mage_GiftMessage_Model_Entity_Attribute_Source_Boolean_Config extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'label' => Mage::helper('giftmessage')->__('Yes'),
                    'value' =>  1,
                ],
                [
                    'label' => Mage::helper('giftmessage')->__('No'),
                    'value' =>  0,
                ],
                [
                    'label' => Mage::helper('giftmessage')->__('Use config'),
                    'value' =>  2,
                ],
            ];
        }
        return $this->_options;
    }
}
