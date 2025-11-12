<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * DHL Abstract class
 *
 * @package    Mage_Usa
 */
abstract class Mage_Usa_Model_Shipping_Carrier_Dhl_Abstract extends Mage_Usa_Model_Shipping_Carrier_Abstract
{
    /**
     * Response condition code for service is unavailable at the requested date
     */
    public const CONDITION_CODE_SERVICE_DATE_UNAVAILABLE = 1003;

    /**
     * Count of days to look forward if day is not unavailable
     */
    public const UNAVAILABLE_DATE_LOOK_FORWARD = 5;

    /**
     * Date format for request
     */
    public const REQUEST_DATE_FORMAT = 'Y-m-d';

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
            date(self::REQUEST_DATE_FORMAT),
        );
    }

    /**
     * Determine shipping day according to configuration settings
     *
     * @param string $shippingDays
     * @param string $date
     * @return string
     */
    protected function _determineShippingDay($shippingDays, $date)
    {
        if (empty($shippingDays)) {
            return $date;
        }

        $shippingDays = explode(',', $shippingDays);

        $index = 0;
        $weekday = date('D', strtotime($date));
        while (!in_array($weekday, $shippingDays) && $index < 10) {
            $index++;
            $weekday = date('D', strtotime("$date +$index day"));
        }

        return date(self::REQUEST_DATE_FORMAT, strtotime("$date +$index day"));
    }
}
