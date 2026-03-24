<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS Address Verification Modal Block
 *
 * Renders the address verification confirmation modal shown during checkout
 * when USPS suggests address corrections.
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Block_Checkout_Address_Verification extends Mage_Core_Block_Template
{
    /**
     * @var array Original address data
     */
    protected $_originalAddress = [];

    /**
     * @var array Corrected address data
     */
    protected $_correctedAddress = [];

    /**
     * @var array List of field corrections
     */
    protected $_corrections = [];

    /**
     * @var array Warning messages from USPS
     */
    protected $_warnings = [];

    /**
     * @var string Verification status (exact|corrected|invalid)
     */
    protected $_status = '';

    /**
     * Check if address verification feature is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) Mage::getStoreConfig('carriers/usps/verify_addresses')
            && Mage::getStoreConfig('carriers/usps/active');
    }

    /**
     * Get AJAX verification URL
     *
     * @return string
     */
    public function getVerifyUrl()
    {
        return $this->getUrl('usa/address/verify', ['_secure' => true]);
    }

    /**
     * Get AJAX apply correction URL
     *
     * @return string
     */
    public function getApplyUrl()
    {
        return $this->getUrl('usa/address/apply', ['_secure' => true]);
    }

    /**
     * Set original address
     *
     * @return $this
     */
    public function setOriginalAddress(array $address)
    {
        $this->_originalAddress = $address;
        return $this;
    }

    /**
     * Get original address
     *
     * @return array
     */
    public function getOriginalAddress()
    {
        return $this->_originalAddress;
    }

    /**
     * Set corrected address
     *
     * @return $this
     */
    public function setCorrectedAddress(array $address)
    {
        $this->_correctedAddress = $address;
        return $this;
    }

    /**
     * Get corrected address
     *
     * @return array
     */
    public function getCorrectedAddress()
    {
        return $this->_correctedAddress;
    }

    /**
     * Set corrections list
     *
     * @return $this
     */
    public function setCorrections(array $corrections)
    {
        $this->_corrections = $corrections;
        return $this;
    }

    /**
     * Get corrections list
     *
     * @return array
     */
    public function getCorrections()
    {
        return $this->_corrections;
    }

    /**
     * Check if there are corrections
     *
     * @return bool
     */
    public function hasCorrections()
    {
        return count($this->_corrections) > 0;
    }

    /**
     * Escape string for use in JavaScript (wrapper for Magento 1 jsQuoteEscape)
     *
     * @param  string $string
     * @return string
     */
    public function escapeJs($string)
    {
        return $this->jsQuoteEscape($string);
    }

    /**
     * Set warning messages
     *
     * @return $this
     */
    public function setWarnings(array $warnings)
    {
        $this->_warnings = $warnings;
        return $this;
    }

    /**
     * Get warning messages
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->_warnings;
    }

    /**
     * Check if there are warnings
     *
     * @return bool
     */
    public function hasWarnings()
    {
        return count($this->_warnings) > 0;
    }

    /**
     * Set verification status
     *
     * @param  string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * Get verification status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Format address for display
     *
     * @return string HTML formatted address
     */
    public function formatAddressHtml(array $address)
    {
        $lines = [];

        if (isset($address['street1']) && $address['street1'] !== '') {
            $lines[] = $this->escapeHtml($address['street1']);
        }

        if (isset($address['street2']) && $address['street2'] !== '') {
            $lines[] = $this->escapeHtml($address['street2']);
        }

        $cityStateZip = [];
        if (isset($address['city']) && $address['city'] !== '') {
            $cityStateZip[] = $this->escapeHtml($address['city']);
        }

        if (isset($address['region']) && $address['region'] !== '') {
            $cityStateZip[] = $this->escapeHtml($address['region']);
        }

        if (isset($address['postcode']) && $address['postcode'] !== '') {
            $cityStateZip[] = $this->escapeHtml($address['postcode']);
        }

        if ($cityStateZip !== []) {
            $lines[] = implode(', ', $cityStateZip);
        }

        return implode('<br/>', array_map(strval(...), $lines));
    }

    /**
     * Get field label for display
     *
     * @param  string $field
     * @return string
     */
    public function getFieldLabel($field)
    {
        $labels = [
            'street1' => $this->__('Street Address'),
            'street2' => $this->__('Address Line 2'),
            'city' => $this->__('City'),
            'region' => $this->__('State/Province'),
            'postcode' => $this->__('ZIP/Postal Code'),
            'country_id' => $this->__('Country'),
        ];

        return $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Get modal title based on status
     *
     * @return string
     */
    public function getModalTitle()
    {
        return match ($this->_status) {
            'corrected' => $this->__('Address Verification'),
            'invalid' => $this->__('Address Not Found'),
            default => $this->__('Verify Your Address'),
        };
    }

    /**
     * Get modal message based on status
     *
     * @return string
     */
    public function getModalMessage()
    {
        return match ($this->_status) {
            'corrected' => $this->__('USPS suggests the following corrections to your address:'),
            'invalid' => $this->__('The address you entered could not be verified. You may continue with your original address or make corrections.'),
            default => $this->__('Please verify your shipping address.'),
        };
    }
}
