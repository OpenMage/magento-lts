<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * Source model for DHL shipping methods for documentation
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Doc extends Mage_Usa_Model_Shipping_Carrier_Dhl_International_Source_Method_Abstract
{
    /**
     * Carrier Product Type Indicator
     *
     * @var string
     */
    protected $_contentType = Mage_Usa_Model_Shipping_Carrier_Dhl_International::DHL_CONTENT_TYPE_DOC;
}
