<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Usa\Model\Shipping\Carrier;

use Generator;

trait UspsSecurityTrait
{
    public static function provideGatewayUrlsData(): Generator
    {
        yield 'production URL' => ['https://apis.usps.com/', true];
        yield 'sandbox URL' => ['https://apis-tem.usps.com/', true];
        yield 'with path' => ['https://apis.usps.com/some/path', true];
        yield 'SSRF evil.com' => ['https://evil.com/', false];
        yield 'SSRF with usps subdomain' => ['https://usps.com.evil.com/', false];
        yield 'non-HTTPS' => ['http://api.usps.com/', false];
        yield 'FTP scheme' => ['ftp://api.usps.com/', false];
        yield 'empty string' => ['', false];
        yield 'no scheme' => ['api.usps.com', false];
        yield 'bare IP' => ['https://192.168.1.1/', false];
        yield 'localhost' => ['https://localhost/', false];
        yield 'internal network' => ['https://10.0.0.1/', false];
    }
}
