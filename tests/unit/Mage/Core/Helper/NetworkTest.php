<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Iterator;
use Mage;
use Mage_Core_Helper_Network as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class NetworkTest extends OpenMageTest
{
    /**
     * @dataProvider provideAddresses
     * @group Helper
     */
    public function testGetAddressClass(string $address, string $expected): void
    {
        self::assertSame($expected, $this->getSubject()->getAddressClass($address));
    }

    public static function provideAddresses(): Iterator
    {
        yield 'public IPv4' => ['93.184.216.34', Subject::ADDRESS_PUBLIC];
        yield 'public IPv6' => ['2606:2800:220:1:248:1893:25c8:1946', Subject::ADDRESS_PUBLIC];
        yield 'RFC 1918' => ['172.16.4.1', Subject::ADDRESS_PRIVATE];
        yield 'carrier-grade NAT' => ['100.64.0.1', Subject::ADDRESS_PRIVATE];
        yield 'unique local IPv6' => ['fd12::1', Subject::ADDRESS_PRIVATE];
        yield 'loopback' => ['127.0.0.2', Subject::ADDRESS_RESTRICTED];
        yield 'link-local metadata' => ['169.254.169.254', Subject::ADDRESS_RESTRICTED];
        yield 'Alibaba metadata inside CGNAT' => ['100.100.100.200', Subject::ADDRESS_RESTRICTED];
        yield 'AWS IPv6 metadata inside ULA' => ['fd00:ec2::254', Subject::ADDRESS_RESTRICTED];
        yield 'bracketed IPv6 loopback' => ['[::1]', Subject::ADDRESS_RESTRICTED];
        yield 'IPv4-mapped loopback' => ['::ffff:127.0.0.1', Subject::ADDRESS_RESTRICTED];
        yield '6to4 wrapping RFC 1918' => ['2002:c0a8:0101::1', Subject::ADDRESS_PRIVATE];
        yield 'not an address' => ['localhost', Subject::ADDRESS_RESTRICTED];
    }

    /**
     * @group Helper
     */
    public function testIsAddressClassAllowed(): void
    {
        $subject = $this->getSubject();

        self::assertTrue($subject->isAddressClassAllowed(Subject::ADDRESS_PUBLIC, false));
        self::assertTrue($subject->isAddressClassAllowed(Subject::ADDRESS_PRIVATE, true));
        self::assertFalse($subject->isAddressClassAllowed(Subject::ADDRESS_PRIVATE, false));
        self::assertFalse($subject->isAddressClassAllowed(Subject::ADDRESS_RESTRICTED, true));
    }

    /**
     * @group Helper
     */
    public function testResolveHostReturnsAddressLiteralsUnchanged(): void
    {
        self::assertSame(['10.0.0.1'], $this->getSubject()->resolveHost('10.0.0.1'));
        self::assertSame(['::1'], $this->getSubject()->resolveHost('[::1]'));
    }

    private function getSubject(): Subject
    {
        /** @var Subject $helper */
        $helper = Mage::helper('core/network');

        return $helper;
    }
}
