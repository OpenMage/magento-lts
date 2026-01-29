<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Shipping Methods source model
 *
 * Provides shipping method options available via the USPS REST API.
 * These differ from the legacy XML API method codes.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Method
{
    /**
     * Get REST API shipping methods
     *
     * Method codes follow the pattern: PRODUCT_TYPE_RATE-INDICATOR_PROCESSING-CATEGORY
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = $this->getRestMethods();
        $arr = [];

        foreach ($methods as $code => $label) {
            $arr[] = [
                'value' => $code,
                'label' => Mage::helper('usa')->__($label),
            ];
        }

        // Sort alphabetically by label for better admin UX
        usort($arr, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $arr;
    }

    /**
     * Get REST API method codes and labels
     *
     * Based on USPS REST API pricing endpoints.
     * Method code format: {MAIL_CLASS}_{RATE_INDICATOR} (official USPS codes)
     *
     * Rate Indicators:
     * SP = Single-piece, FE = Flat Rate Envelope, FP = Flat Rate Padded Envelope,
     * FA = Flat Rate Legal Envelope, FS = Flat Rate Small Box, FB = Flat Rate Medium Box,
     * PL = Flat Rate Large Box, PM = APO/FPO/DPO, CP/P5 = Cubic Priority,
     * NDC = Network Distribution Center, SCF = Sectional Center Facility
     *
     * @return array
     */
    public function getRestMethods()
    {
        return [
            // USPS Ground Advantage - Keep only SP to avoid duplicates
            'USPS_GROUND_ADVANTAGE_SP' => 'USPS Ground Advantage',
            
            // Priority Mail
            'PRIORITY_MAIL_SP' => 'Priority Mail',
            'PRIORITY_MAIL_FE' => 'Priority Mail - Flat Rate Envelope',
            'PRIORITY_MAIL_FA' => 'Priority Mail - Legal Flat Rate Envelope',
            'PRIORITY_MAIL_FP' => 'Priority Mail - Padded Flat Rate Envelope',
            'PRIORITY_MAIL_FS' => 'Priority Mail - Small Flat Rate Box',
            'PRIORITY_MAIL_FB' => 'Priority Mail - Medium Flat Rate Box',
            'PRIORITY_MAIL_PL' => 'Priority Mail - Large Flat Rate Box',
            
            // Priority Mail Express
            'PRIORITY_MAIL_EXPRESS_SP' => 'Priority Mail Express',
            'PRIORITY_MAIL_EXPRESS_FE' => 'Priority Mail Express - Flat Rate Envelope',
            'PRIORITY_MAIL_EXPRESS_FA' => 'Priority Mail Express - Legal Flat Rate Envelope',
            'PRIORITY_MAIL_EXPRESS_FP' => 'Priority Mail Express - Padded Flat Rate Envelope',
            'PRIORITY_MAIL_EXPRESS_FB' => 'Priority Mail Express - Flat Rate Box',
            
            // First-Class Package
            'FIRST_CLASS_PACKAGE_SERVICE_SP' => 'First-Class Package Service',
            
            // Library & Media Mail
            'LIBRARY_MAIL_SP' => 'Library Mail',
            'MEDIA_MAIL_SP' => 'Media Mail',
            
            // Parcel Select
            'PARCEL_SELECT_SP' => 'Parcel Select',
            
            // International Services
            'FIRST_CLASS_PACKAGE_INTERNATIONAL_SERVICE_SP' => 'First-Class Package International',
            'PRIORITY_MAIL_INTERNATIONAL_SP' => 'Priority Mail International',
            'PRIORITY_MAIL_INTERNATIONAL_FE' => 'Priority Mail International - Flat Rate Envelope',
            'PRIORITY_MAIL_INTERNATIONAL_FA' => 'Priority Mail International - Legal Flat Rate Envelope',
            'PRIORITY_MAIL_INTERNATIONAL_FP' => 'Priority Mail International - Padded Flat Rate Envelope',
            'PRIORITY_MAIL_INTERNATIONAL_FS' => 'Priority Mail International - Small Flat Rate Box',
            'PRIORITY_MAIL_INTERNATIONAL_FB' => 'Priority Mail International - Medium Flat Rate Box',
            'PRIORITY_MAIL_INTERNATIONAL_PL' => 'Priority Mail International - Large Flat Rate Box',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_PA' => 'Priority Mail Express International',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E4' => 'Priority Mail Express International - Flat Rate Envelope',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_E6' => 'Priority Mail Express International - Legal Flat Rate Envelope',
            'PRIORITY_MAIL_EXPRESS_INTERNATIONAL_FP' => 'Priority Mail Express International - Padded Flat Rate Envelope',
            'GLOBAL_EXPRESS_GUARANTEED_SP' => 'Global Express Guaranteed',
        ];
    }

    /**
     * Get method code to label mapping for rate parsing
     *
     * @return array
     */
    public function getMethodCodeToLabel()
    {
        return $this->getRestMethods();
    }
}
