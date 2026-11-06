<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

/**
 * Display-only legacy PayPal payment information block.
 */
class Mage_Paypal_Block_Payment_Info extends Mage_Payment_Block_Info_Cc
{
    /**
     * @var array<string, true>
     */
    private const CREDIT_CARD_METHODS = [
        'paypal_direct' => true,
        'paypaluk_direct' => true,
        'verisign' => true,
        'payflow_link' => true,
        'payflow_advanced' => true,
        'hosted_pro' => true,
    ];

    /**
     * @var list<string>
     */
    private const PAYMENT_INFO_KEYS = [
        'paypal_payer_id',
        'paypal_payer_email',
        'paypal_payer_status',
        'paypal_address_id',
        'paypal_address_status',
        'paypal_protection_eligibility',
        'paypal_fraud_filters',
        'paypal_correlation_id',
        'paypal_avs_code',
        'paypal_cvv2_match',
        'centinel_vpas_result',
        'centinel_eci_result',
        'buyer_tax_id',
        'buyer_tax_id_type',
    ];

    /**
     * @var list<string>
     */
    private const PUBLIC_PAYMENT_INFO_KEYS = [
        'paypal_payer_email',
        'buyer_tax_id',
        'buyer_tax_id_type',
    ];

    /**
     * Don't show CC type for non-CC legacy PayPal methods.
     */
    #[Override]
    public function getCcTypeName(): string
    {
        if (isset(self::CREDIT_CARD_METHODS[$this->getInfo()->getMethod()])) {
            return parent::getCcTypeName();
        }

        return '';
    }

    /**
     * Prepare legacy PayPal-specific payment information.
     *
     * @param array<mixed>|Varien_Object $transport
     */
    #[Override]
    protected function _prepareSpecificInformation($transport = null): Varien_Object
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $keys = $this->getIsSecureMode() ? self::PUBLIC_PAYMENT_INFO_KEYS : self::PAYMENT_INFO_KEYS;
        $info = $this->getLegacyPaymentInfo($keys);

        if (!$this->getIsSecureMode()) {
            $info[Mage::helper('paypal')->__('Last Transaction ID')] = $this->getInfo()->getLastTransId();
        }

        return $transport->addData($info);
    }

    /**
     * @param  list<string>         $keys
     * @return array<string, mixed>
     */
    private function getLegacyPaymentInfo(array $keys): array
    {
        $result = [];
        $payment = $this->getInfo();
        foreach ($keys as $key) {
            if (!$payment->hasAdditionalInformation($key)) {
                continue;
            }

            $value = $this->getLegacyPaymentInfoValue($payment->getAdditionalInformation($key), $key);
            if ($value === []) {
                continue;
            }

            if ($value === '') {
                continue;
            }

            if ($value === '0') {
                continue;
            }

            $result[$this->getLegacyPaymentInfoLabel($key)] = $value;
        }

        return $result;
    }

    private function getLegacyPaymentInfoLabel(string $key): string
    {
        return match ($key) {
            'paypal_payer_id' => Mage::helper('paypal')->__('Payer ID'),
            'paypal_payer_email' => Mage::helper('paypal')->__('Payer Email'),
            'paypal_payer_status' => Mage::helper('paypal')->__('Payer Status'),
            'paypal_address_id' => Mage::helper('paypal')->__('Payer Address ID'),
            'paypal_address_status' => Mage::helper('paypal')->__('Payer Address Status'),
            'paypal_protection_eligibility' => Mage::helper('paypal')->__('Merchant Protection Eligibility'),
            'paypal_fraud_filters' => Mage::helper('paypal')->__('Triggered Fraud Filters'),
            'paypal_correlation_id' => Mage::helper('paypal')->__('Last Correlation ID'),
            'paypal_avs_code' => Mage::helper('paypal')->__('Address Verification System Response'),
            'paypal_cvv2_match' => Mage::helper('paypal')->__('CVV2 Check Result by PayPal'),
            'buyer_tax_id' => Mage::helper('paypal')->__("Buyer's Tax ID"),
            'buyer_tax_id_type' => Mage::helper('paypal')->__("Buyer's Tax ID Type"),
            'centinel_vpas_result' => Mage::helper('paypal')->__(
                'PayPal/Centinel Visa Payer Authentication Service Result',
            ),
            'centinel_eci_result' => Mage::helper('paypal')->__('PayPal/Centinel Electronic Commerce Indicator'),
            default => '',
        };
    }

    private function getLegacyPaymentInfoValue(mixed $value, string $key): mixed
    {
        if (!is_scalar($value) && $value !== null) {
            return $value;
        }

        $value = (string) $value;
        $label = match ($key) {
            'paypal_avs_code' => $this->getAvsLabel($value),
            'paypal_cvv2_match' => $this->getCvv2Label($value),
            'centinel_vpas_result' => $this->getCentinelVpasLabel($value),
            'centinel_eci_result' => $this->getCentinelEciLabel($value),
            'buyer_tax_id_type' => $this->getBuyerTaxIdTypeLabel($value),
            default => '',
        };

        if ($label === '') {
            return $value;
        }

        if ($key === 'buyer_tax_id_type') {
            return $label;
        }

        return sprintf('#%s%s', $value, $value === $label ? '' : ': ' . $label);
    }

    private function getAvsLabel(string $value): string
    {
        return match ($value) {
            'A', 'YN' => Mage::helper('paypal')->__('Matched Address only (no ZIP)'),
            'B' => Mage::helper('paypal')->__('Matched Address only (no ZIP). International'),
            'N' => Mage::helper('paypal')->__('No Details matched'),
            'C' => Mage::helper('paypal')->__('No Details matched. International'),
            'X' => Mage::helper('paypal')->__('Exact Match. Address and nine-digit ZIP code'),
            'D' => Mage::helper('paypal')->__('Exact Match. Address and Postal Code. International'),
            'F' => Mage::helper('paypal')->__('Exact Match. Address and Postal Code. UK-specific'),
            'E' => Mage::helper('paypal')->__('N/A. Not allowed for MOTO (Internet/Phone) transactions'),
            'G' => Mage::helper('paypal')->__('N/A. Global Unavailable'),
            'I' => Mage::helper('paypal')->__('N/A. International Unavailable'),
            'Z', 'NY' => Mage::helper('paypal')->__('Matched five-digit ZIP only (no Address)'),
            'P' => Mage::helper('paypal')->__('Matched Postal Code only (no Address)'),
            'R' => Mage::helper('paypal')->__('N/A. Retry'),
            'S' => Mage::helper('paypal')->__('N/A. Service not Supported'),
            'U' => Mage::helper('paypal')->__('N/A. Unavailable'),
            'W' => Mage::helper('paypal')->__('Matched whole nine-didgit ZIP (no Address)'),
            'Y' => Mage::helper('paypal')->__('Yes. Matched Address and five-didgit ZIP'),
            '0' => Mage::helper('paypal')->__('All the address information matched'),
            '1' => Mage::helper('paypal')->__('None of the address information matched'),
            '2' => Mage::helper('paypal')->__('Part of the address information matched'),
            '3' => Mage::helper('paypal')->__('N/A. The merchant did not provide AVS information'),
            '4' => Mage::helper('paypal')->__(
                'N/A. Address not checked, or acquirer had no response. Service not available',
            ),
            default => $value,
        };
    }

    private function getCvv2Label(string $value): string
    {
        return match ($value) {
            'M' => Mage::helper('paypal')->__('Matched (CVV2CSC)'),
            'N' => Mage::helper('paypal')->__('No match'),
            'P' => Mage::helper('paypal')->__('N/A. Not processed'),
            'S' => Mage::helper('paypal')->__('N/A. Service not supported'),
            'U' => Mage::helper('paypal')->__('N/A. Service not available'),
            'X' => Mage::helper('paypal')->__('N/A. No response'),
            '0' => Mage::helper('paypal')->__('Matched (CVV2)'),
            '1' => Mage::helper('paypal')->__('No match'),
            '2' => Mage::helper('paypal')->__('N/A. The merchant has not implemented CVV2 code handling'),
            '3' => Mage::helper('paypal')->__('N/A. Merchant has indicated that CVV2 is not present on card'),
            '4' => Mage::helper('paypal')->__('N/A. Service not available'),
            default => $value,
        };
    }

    private function getCentinelVpasLabel(string $value): string
    {
        return match ($value) {
            '2', 'D' => Mage::helper('paypal')->__('Authenticated, Good Result'),
            '1' => Mage::helper('paypal')->__('Authenticated, Bad Result'),
            '3', '6', '8', 'A', 'C' => Mage::helper('paypal')->__('Attempted Authentication, Good Result'),
            '4', '7', '9' => Mage::helper('paypal')->__('Attempted Authentication, Bad Result'),
            '', '0', 'B' => Mage::helper('paypal')->__('No Liability Shift'),
            default => $value,
        };
    }

    private function getCentinelEciLabel(string $value): string
    {
        return match ($value) {
            '01', '07' => Mage::helper('paypal')->__('Merchant Liability'),
            '02', '05', '06' => Mage::helper('paypal')->__('Issuer Liability'),
            default => $value,
        };
    }

    private function getBuyerTaxIdTypeLabel(string $value): string
    {
        return match ($value) {
            'BR_CNPJ' => Mage::helper('paypal')->__('CNPJ'),
            'BR_CPF' => Mage::helper('paypal')->__('CPF'),
            default => '',
        };
    }
}
