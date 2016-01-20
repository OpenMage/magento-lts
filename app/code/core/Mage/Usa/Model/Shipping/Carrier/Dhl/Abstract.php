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
 * @package     Mage_Usa
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DHL Abstract class
 *
 * @category Mage
 * @package  Mage_Usa
 * @author   Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Dhl_Abstract extends Mage_Usa_Model_Shipping_Carrier_Abstract
{
    /**
     * Response condition code for service is unavailable at the requested date
     */
    const CONDITION_CODE_SERVICE_DATE_UNAVAILABLE = 1003;

    /**
     * Count of days to look forward if day is not unavailable
     */
    const UNAVAILABLE_DATE_LOOK_FORWARD = 5;

    /**
     * Date format for request
     */
    const REQUEST_DATE_FORMAT = 'Y-m-d';

    /**
     * Get shipping date
     *
     * @param bool $domestic
     * @return string
     */
    protected function _getShipDate($domestic = true)
    {
        return $this->_determineShippingDay(
            $this->getConfigData($domestic ? 'shipment_days' : 'intl_shipment_days'),
            date(self::REQUEST_DATE_FORMAT)
        );
    }

    /**
     * Determine shipping day according to configuration settings
     *
     * @param array $shippingDays
     * @param string $date
     * @return string
     */
    protected function _determineShippingDay($shippingDays, $date)
    {
        if (empty($shippingDays)) {
            return $date;
        }

        $shippingDays = explode(',', $shippingDays);

        $i = 0;
        $weekday = date('D', strtotime($date));
        while (!in_array($weekday, $shippingDays) && $i < 10) {
            $i++;
            $weekday = date('D', strtotime("$date +$i day"));
        }

        return date(self::REQUEST_DATE_FORMAT, strtotime("$date +$i day"));
    }
}
