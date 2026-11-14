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
        yield 'system xml encrypted field' => ['paypal/wpp/api_password'];
        yield 'default config encrypted field' => ['payment/payflow_link/pwd'];
        yield 'default config encrypted api username' => ['paypal/wpp/api_username'];
        yield 'default config encrypted api signature' => ['paypal/wpp/api_signature'];
        yield 'config_path override in nested system xml group' => ['paypal/fetch_reports/ftp_password'];
        yield 'parent group returns decrypted children' => ['paypal/wpp'];
        yield 'parent section returns decrypted children' => ['paypal'];
        yield 'surrounding slashes' => ['/paypal/wpp/api_password/'];
    }

    public static function provideUnencryptedConfigPaths(): Generator
    {
        yield 'store name' => ['general/store_information/name'];
        yield 'sibling sharing a prefix' => ['paypal/wpp/api_password_not'];
        yield 'empty' => [''];
    }
}
