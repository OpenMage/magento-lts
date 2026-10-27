<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\Email;

use Generator;

trait PathValidatorTrait
{
    public static function provideEncryptedConfigPaths(): Generator
    {
        yield 'system xml encrypted field' => ['payment_services/centinel/password'];
        yield 'default config encrypted field' => ['carriers/ups/password'];
        yield 'default config encrypted api username' => ['carriers/ups/username'];
        yield 'default config encrypted api key' => ['carriers/ups/access_license_number'];
        yield 'config_path override in nested system xml group' => ['payment/paypal/client_secret'];
        yield 'parent group returns decrypted children' => ['payment/paypal'];
        yield 'parent section returns decrypted children' => ['payment'];
        yield 'surrounding slashes' => ['/payment/paypal/client_secret/'];
    }

    public static function provideUnencryptedConfigPaths(): Generator
    {
        yield 'store name' => ['general/store_information/name'];
        yield 'sibling sharing a prefix' => ['payment/paypal/client_secret_not'];
        yield 'empty' => [''];
    }
}
