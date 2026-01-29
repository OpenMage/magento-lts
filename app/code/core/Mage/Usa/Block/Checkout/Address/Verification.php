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
     * @param array $address
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
     * @param array $address
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
     * @param array $corrections
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
     * Set warning messages
     *
     * @param array $warnings
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
     * @param string $status
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
     * @param array $address
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

        if (count($cityStateZip) > 0) {
            $lines[] = implode(', ', $cityStateZip);
        }

        return implode('<br/>', $lines);
    }

    /**
     * Get field label for display
     *
     * @param string $field
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
        switch ($this->_status) {
            case 'corrected':
                return $this->__('Address Verification');
            case 'invalid':
                return $this->__('Address Not Found');
            default:
                return $this->__('Verify Your Address');
        }
    }

    /**
     * Get modal message based on status
     *
     * @return string
     */
    public function getModalMessage()
    {
        switch ($this->_status) {
            case 'corrected':
                return $this->__('USPS suggests the following corrections to your address:');
            case 'invalid':
                return $this->__('The address you entered could not be verified. You may continue with your original address or make corrections.');
            default:
                return $this->__('Please verify your shipping address.');
        }
    }
}
