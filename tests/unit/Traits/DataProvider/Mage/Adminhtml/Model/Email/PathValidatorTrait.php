<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\Email;

trait PathValidatorTrait
{
    public static function provideEncryptedConfigPaths(): array
    {
        return [
            'system xml encrypted field' => ['paypal/wpp/api_password'],
            'default config encrypted field' => ['payment/payflow_link/pwd'],
            'default config encrypted api username' => ['paypal/wpp/api_username'],
            'default config encrypted api signature' => ['paypal/wpp/api_signature'],
        ];
    }
}
