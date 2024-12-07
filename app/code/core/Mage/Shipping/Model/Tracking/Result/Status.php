<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fields:
 * - carrier: fedex
 * - carrierTitle: Federal Express
 * - tracking: 749011111111
 * - status: delivered
 * - service: home delivery
 * - delivery date: 2007-11-23
 * - delivery time: 16:01:00
 * - delivery location: Frontdoor
 * - signedby: lindy
 *
 * Fields:
 * -carrier: ups cgi
 * -popup: 1
 * -url: http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&error_carried=true&tracknums_displayed=5&TypeOfInquiryNumber=T&loc=en_US&InquiryNumber1=$tracking
 *
 * Fields:
 * -carrier: usps
 * -tracksummary: Your item was delivered at 6:50 am on February 6 in Los Angeles CA 90064
 *
 * @category   Mage
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Tracking_Result_Status extends Mage_Shipping_Model_Tracking_Result_Abstract
{
    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }
}
