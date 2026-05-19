<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Imports PayPal-approved checkout addresses into the live quote.
 */
class Mage_Paypal_Model_Express_AddressImporter extends Mage_Core_Model_Abstract
{
    private const DEFAULT_TELEPHONE = '0000000000';

    /**
     * Import PayPal order address data into the quote.
     *
     * @return bool                true when a shipping address was imported
     * @throws Mage_Core_Exception
     */
    public function importFromOrderDetails(Mage_Sales_Model_Quote $quote, string $responseBody): bool
    {
        $details = json_decode($responseBody, true);
        if (!is_array($details)) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal order details were not valid JSON.'));
        }

        $paypalAccount = $this->_extractPaypalAccount($details);
        $shipping = $this->_extractShipping($details);
        $email = $this->_resolveEmail($quote, $paypalAccount);
        $phone = $this->_resolvePhone($paypalAccount);

        if ($quote->isVirtual()) {
            $billingAddress = $this->_extractAddress($paypalAccount);
            if ($billingAddress === []) {
                $billingAddress = $shipping['address'];
            }

            $this->_applyAddress(
                $quote->getBillingAddress(),
                $billingAddress,
                $this->_resolveName($paypalAccount, $shipping['name']),
                $email,
                $phone,
            );
            $quote->setCustomerEmail($email);
            return false;
        }

        if ($shipping['address'] === []) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal did not return a shipping address for this order.'));
        }

        $shippingName = $this->_resolveName($paypalAccount, $shipping['name']);
        $this->_applyAddress($quote->getShippingAddress(), $shipping['address'], $shippingName, $email, $phone);

        $billingAddress = $this->_extractAddress($paypalAccount);
        $billingName = $this->_resolveName($paypalAccount, $shipping['name']);
        $this->_applyAddress(
            $quote->getBillingAddress(),
            $billingAddress !== [] ? $billingAddress : $shipping['address'],
            $billingName,
            $email,
            $phone,
        );
        $quote->setCustomerEmail($email);

        return true;
    }

    /**
     * @param  array<string, mixed> $details
     * @return array<string, mixed>
     */
    private function _extractPaypalAccount(array $details): array
    {
        if (is_array($details['payment_source']['paypal'] ?? null)) {
            return $details['payment_source']['paypal'];
        }

        if (is_array($details['payer'] ?? null)) {
            return $details['payer'];
        }

        return [];
    }

    /**
     * @param  array<string, mixed>                                             $details
     * @return array{name: array<string, mixed>, address: array<string, mixed>}
     */
    private function _extractShipping(array $details): array
    {
        $unit = is_array($details['purchase_units'][0] ?? null) ? $details['purchase_units'][0] : [];
        $shipping = is_array($unit['shipping'] ?? null) ? $unit['shipping'] : [];

        return [
            'name' => is_array($shipping['name'] ?? null) ? $shipping['name'] : [],
            'address' => is_array($shipping['address'] ?? null) ? $shipping['address'] : [],
        ];
    }

    /**
     * @param  array<string, mixed> $paypalAccount
     * @return array<string, mixed>
     */
    private function _extractAddress(array $paypalAccount): array
    {
        return is_array($paypalAccount['address'] ?? null) ? $paypalAccount['address'] : [];
    }

    /**
     * @param  array<string, mixed>                       $paypalAccount
     * @param  array<string, mixed>                       $shippingName
     * @return array{firstname: string, lastname: string}
     */
    private function _resolveName(array $paypalAccount, array $shippingName): array
    {
        $name = is_array($paypalAccount['name'] ?? null) ? $paypalAccount['name'] : [];
        $firstname = trim((string) ($name['given_name'] ?? ''));
        $lastname = trim((string) ($name['surname'] ?? ''));
        if ($firstname !== '' && $lastname !== '') {
            return ['firstname' => $firstname, 'lastname' => $lastname];
        }

        $fullName = trim((string) ($shippingName['full_name'] ?? ''));
        if ($fullName === '') {
            $fullName = trim($firstname . ' ' . $lastname);
        }

        if ($fullName === '') {
            return ['firstname' => 'PayPal', 'lastname' => 'Customer'];
        }

        /** @var false|non-empty-list<string> $parts */
        $parts = preg_split('/\s+/', $fullName, 2);
        if (!is_array($parts) || $parts === []) {
            return ['firstname' => 'PayPal', 'lastname' => 'Customer'];
        }

        return [
            'firstname' => $parts[0],
            'lastname' => $parts[1] ?? 'Customer',
        ];
    }

    /**
     * @param  array<string, mixed> $paypalAccount
     * @throws Mage_Core_Exception
     */
    private function _resolveEmail(Mage_Sales_Model_Quote $quote, array $paypalAccount): string
    {
        $email = trim((string) ($paypalAccount['email_address'] ?? ''));
        if ($email === '') {
            $quoteEmail = $quote->getCustomerEmail();
            if ($quoteEmail === null || $quoteEmail === '') {
                $quoteEmail = $quote->getBillingAddress()->getEmail();
            }

            $email = trim((string) $quoteEmail);
        }

        if ($email === '') {
            Mage::throwException(Mage::helper('paypal')->__('PayPal did not return a buyer email address.'));
        }

        return $email;
    }

    /**
     * @param array<string, mixed> $paypalAccount
     */
    private function _resolvePhone(array $paypalAccount): string
    {
        $phone = $paypalAccount['phone_number']['national_number'] ?? null;
        if (is_scalar($phone) && trim((string) $phone) !== '') {
            return trim((string) $phone);
        }

        return self::DEFAULT_TELEPHONE;
    }

    /**
     * @param array<string, mixed>                       $paypalAddress
     * @param array{firstname: string, lastname: string} $name
     */
    private function _applyAddress(
        Mage_Sales_Model_Quote_Address $address,
        array $paypalAddress,
        array $name,
        string $email,
        string $telephone
    ): void {
        $countryId = strtoupper(trim((string) ($paypalAddress['country_code'] ?? '')));
        $regionValue = trim((string) ($paypalAddress['admin_area_1'] ?? ''));
        $region = $this->_resolveRegion($regionValue, $countryId);

        $address->addData([
            'firstname' => $name['firstname'],
            'lastname' => $name['lastname'],
            'email' => $email,
            'telephone' => $telephone,
            'street' => array_values(array_filter([
                trim((string) ($paypalAddress['address_line_1'] ?? '')),
                trim((string) ($paypalAddress['address_line_2'] ?? '')),
            ], static fn(string $line): bool => $line !== '')),
            'city' => trim((string) ($paypalAddress['admin_area_2'] ?? '')),
            'postcode' => trim((string) ($paypalAddress['postal_code'] ?? '')),
            'country_id' => $countryId,
            'region' => $region['name'] !== '' ? $region['name'] : $regionValue,
            'region_id' => $region['id'],
            'region_code' => $region['code'] !== '' ? $region['code'] : $regionValue,
            'save_in_address_book' => 0,
            'customer_address_id' => null,
        ]);
        $address->setSaveInAddressBook(0)
            ->setCustomerAddressId(null)
            ->setCustomerAddress(null);
    }

    /**
     * @return array{id: null|int, code: string, name: string}
     */
    private function _resolveRegion(string $regionValue, string $countryId): array
    {
        if ($regionValue === '' || $countryId === '') {
            return ['id' => null, 'code' => '', 'name' => ''];
        }

        /** @var Mage_Directory_Model_Region $region */
        $region = Mage::getModel('directory/region')->loadByCode($regionValue, $countryId);
        if ($region->getId() === null) {
            $region = Mage::getModel('directory/region')->loadByName($regionValue, $countryId);
        }

        if ($region->getId() === null) {
            return ['id' => null, 'code' => '', 'name' => $regionValue];
        }

        return [
            'id' => (int) $region->getId(),
            'code' => (string) $region->getCode(),
            'name' => (string) $region->getName(),
        ];
    }
}
