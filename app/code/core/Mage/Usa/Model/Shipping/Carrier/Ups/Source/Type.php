<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Usa Ups type action Dropdown source
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Type
{
    public function toOptionArray()
    {
        return [
            ['value' => 'UPS', 'label' => Mage::helper('usa')->__('United Parcel Service')],
            #array('value' => Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_ORDER, 'label' => Mage::helper('usa')->__('Order')),
            ['value' => 'UPS_XML', 'label' => Mage::helper('usa')->__('United Parcel Service XML')],
        ];
    }
}
