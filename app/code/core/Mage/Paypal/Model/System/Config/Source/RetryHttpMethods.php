<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Model_System_Config_Source_RetryHttpMethods
{
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    public const METHOD_PUT = 'PUT';

    public const METHOD_PATCH = 'PATCH';

    public const METHOD_DELETE = 'DELETE';

    public const METHODS = [
        self::METHOD_GET,
        self::METHOD_POST,
        self::METHOD_PUT,
        self::METHOD_PATCH,
        self::METHOD_DELETE,
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::METHOD_GET,
                'label' => Mage::helper('paypal')->__('GET'),
            ],
            [
                'value' => self::METHOD_POST,
                'label' => Mage::helper('paypal')->__('POST'),
            ],
            [
                'value' => self::METHOD_PUT,
                'label' => Mage::helper('paypal')->__('PUT'),
            ],
            [
                'value' => self::METHOD_PATCH,
                'label' => Mage::helper('paypal')->__('PATCH'),
            ],
            [
                'value' => self::METHOD_DELETE,
                'label' => Mage::helper('paypal')->__('DELETE'),
            ],
        ];
    }
}
