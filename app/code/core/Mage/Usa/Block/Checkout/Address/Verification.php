<?php

declare(strict_types=1);

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
    protected array $_originalAddress = [];

    /**
     * @var array Corrected address data
     */
    protected array $_correctedAddress = [];

    /**
     * @var array List of field corrections
     */
    protected array $_corrections = [];

    /**
     * @var array Warning messages from USPS
     */
    protected array $_warnings = [];

    /**
     * @var string Verification status (exact|corrected|invalid)
     */
    protected string $_status = '';

    /**
     * Check if address verification feature is enabled
     */
    public function isEnabled(): bool
    {
        return Mage::getStoreConfigFlag('carriers/usps/verify_addresses')
            && Mage::getStoreConfigFlag('carriers/usps/active');
    }

    /**
     * Get AJAX verification URL
     */
    public function getVerifyUrl(): string
    {
        return $this->getUrl('usa/address/verify', ['_secure' => true]);
    }

    /**
     * Get AJAX apply correction URL
     */
    public function getApplyUrl(): string
    {
        return $this->getUrl('usa/address/apply', ['_secure' => true]);
    }

    /**
     * Set original address
     *
     * @return $this
     */
    public function setOriginalAddress(array $address): self
    {
        $this->_originalAddress = $address;
        return $this;
    }

    /**
     * Get original address
     */
    public function getOriginalAddress(): array
    {
        return $this->_originalAddress;
    }

    /**
     * Set corrected address
     *
     * @return $this
     */
    public function setCorrectedAddress(array $address): self
    {
        $this->_correctedAddress = $address;
        return $this;
    }

    /**
     * Get corrected address
     */
    public function getCorrectedAddress(): array
    {
        return $this->_correctedAddress;
    }

    /**
     * Set corrections list
     *
     * @return $this
     */
    public function setCorrections(array $corrections): self
    {
        $this->_corrections = $corrections;
        return $this;
    }

    /**
     * Get corrections list
     */
    public function getCorrections(): array
    {
        return $this->_corrections;
    }

    /**
     * Check if there are corrections
     */
    public function hasCorrections(): bool
    {
        return $this->_corrections !== [];
    }

    /**
     * Escape string for use in JavaScript (wrapper for Magento 1 jsQuoteEscape)
     */
    public function escapeJs(string $string): string
    {
        return $this->jsQuoteEscape($string);
    }

    /**
     * Set warning messages
     *
     * @return $this
     */
    public function setWarnings(array $warnings): self
    {
        $this->_warnings = $warnings;
        return $this;
    }

    /**
     * Get warning messages
     */
    public function getWarnings(): array
    {
        return $this->_warnings;
    }

    /**
     * Check if there are warnings
     */
    public function hasWarnings(): bool
    {
        return $this->_warnings !== [];
    }

    /**
     * Set verification status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * Get verification status
     */
    public function getStatus(): string
    {
        return $this->_status;
    }

    /**
     * Format address for display
     *
     * @return string HTML formatted address
     */
    public function formatAddressHtml(array $address): string
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
     */
    public function getFieldLabel(string $field): string
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
     */
    public function getModalTitle(): string
    {
        return match ($this->_status) {
            'corrected' => $this->__('Address Verification'),
            'invalid' => $this->__('Address Not Found'),
            default => $this->__('Verify Your Address'),
        };
    }

    /**
     * Get modal message based on status
     */
    public function getModalMessage(): string
    {
        return match ($this->_status) {
            'corrected' => $this->__('USPS suggests the following corrections to your address:'),
            'invalid' => $this->__('The address you entered could not be verified. You may continue with your original address or make corrections.'),
            default => $this->__('Please verify your shipping address.'),
        };
    }
}
