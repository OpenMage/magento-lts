<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_System_Config_Backend_RetryStatusCodes extends Mage_Core_Model_Config_Data
{
    #[Override]
    protected function _beforeSave(): Mage_Core_Model_Config_Data
    {
        $this->setValue(implode(',', $this->parseStatusCodes($this->getData('value'))));

        return parent::_beforeSave();
    }

    /**
     * @return int[]
     * @throws Mage_Core_Exception
     */
    private function parseStatusCodes(mixed $value): array
    {
        $rawStatusCodes = is_array($value) ? $value : explode(',', (string) $value);
        $statusCodes = [];

        foreach ($rawStatusCodes as $rawStatusCode) {
            foreach (explode(',', (string) $rawStatusCode) as $statusCode) {
                $statusCode = trim($statusCode);
                if ($statusCode === '') {
                    continue;
                }

                if (!ctype_digit($statusCode)) {
                    throw new Mage_Core_Exception(
                        Mage::helper('paypal')->__(
                            'Retry status codes must be a comma-separated list of HTTP status codes.',
                        ),
                    );
                }

                $statusCodeValue = (int) $statusCode;
                if ($statusCodeValue < 100 || $statusCodeValue > 599) {
                    throw new Mage_Core_Exception(
                        Mage::helper('paypal')->__(
                            'Retry status codes must be valid HTTP status codes between 100 and 599.',
                        ),
                    );
                }

                if (!in_array($statusCodeValue, $statusCodes, true)) {
                    $statusCodes[] = $statusCodeValue;
                }
            }
        }

        return $statusCodes;
    }
}
