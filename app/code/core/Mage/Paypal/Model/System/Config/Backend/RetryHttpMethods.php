<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_System_Config_Backend_RetryHttpMethods extends Mage_Core_Model_Config_Data
{
    #[Override]
    protected function _beforeSave(): Mage_Core_Model_Config_Data
    {
        $this->setValue(implode(',', $this->parseHttpMethods($this->getData('value'))));

        return parent::_beforeSave();
    }

    /**
     * @return string[]
     * @throws Mage_Core_Exception
     */
    private function parseHttpMethods(mixed $value): array
    {
        $rawMethods = is_array($value) ? $value : explode(',', (string) $value);
        $methods = [];

        foreach ($rawMethods as $rawMethod) {
            foreach (explode(',', (string) $rawMethod) as $method) {
                $method = strtoupper(trim($method));
                if ($method === '') {
                    continue;
                }

                if (!in_array($method, Mage_Paypal_Model_System_Config_Source_RetryHttpMethods::METHODS, true)) {
                    throw new Mage_Core_Exception(
                        Mage::helper('paypal')->__(
                            'Retry HTTP methods must be selected from GET, POST, PUT, PATCH, or DELETE.',
                        ),
                    );
                }

                if (!in_array($method, $methods, true)) {
                    $methods[] = $method;
                }
            }
        }

        return $methods;
    }
}
