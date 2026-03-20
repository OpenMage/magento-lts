<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Price Type source model
 *
 * Provides options for selecting the pricing tier used
 * for USPS REST API rate calculations.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Pricetype
{
    /**
     * EPS Commercial pricing
     */
    public const PRICE_EPS = 'EPS';

    /**
     * Commercial base pricing
     */
    public const PRICE_COMMERCIAL = 'COMMERCIAL';

    /**
     * Get option array for admin configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PRICE_EPS,
                'label' => Mage::helper('usa')->__('Commercial EPS Pricing'),
            ],
            [
                'value' => self::PRICE_COMMERCIAL,
                'label' => Mage::helper('usa')->__('Commercial Base Pricing'),
            ],
        ];
    }
}
