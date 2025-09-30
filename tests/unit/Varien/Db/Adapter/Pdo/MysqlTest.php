<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Db\Adapter\Pdo;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Varien_Db_Adapter_Pdo_Mysql;
use Varien_Object;

final class MysqlTest extends TestCase
{
    public Varien_Db_Adapter_Pdo_Mysql $adapter;

    protected function setUp(): void
    {
        $config = [
            'host' => 'localhost',
            'username' => 'user',
            'password' => 'password',
            'dbname' => 'test_db',
            'type' => 'pdo_mysql',
            'active' => '1',
        ];

        // Create a mock object for Varien_Db_Adapter_Pdo_Mysql
        $this->adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);

        // Call the constructor manually with our config
        $reflectedAdapter = new \ReflectionClass(Varien_Db_Adapter_Pdo_Mysql::class);
        /** @var ReflectionMethod $constructor */
        $constructor = $reflectedAdapter->getConstructor();
        $constructor->invoke($this->adapter, $config);
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithUnixSocket(): void
    {
        $fakeSocket = '/var/run/mysqld/mysqld.sock';
        $hostInfo = $hostInfo = $this->getHostInfo($fakeSocket);

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_UNIX_SOCKET, $hostInfo->getAddressType());
        self::assertSame($fakeSocket, $hostInfo->getUnixSocket());
        self::assertNull($hostInfo->getHostName());
        self::assertNull($hostInfo->getPort());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv4Address(): void
    {
        $hostInfo = $this->getHostInfo('192.168.1.1:3306');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('192.168.1.1', $hostInfo->getHostName());
        self::assertSame('3306', $hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv4AddressWithoutPort(): void
    {
        $hostInfo = $this->getHostInfo('192.168.1.1');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('192.168.1.1', $hostInfo->getHostName());
        self::assertNull($hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithHostname(): void
    {
        $hostInfo = $this->getHostInfo('db.example.com:3306');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME, $hostInfo->getAddressType());
        self::assertSame('db.example.com', $hostInfo->getHostName());
        self::assertSame('3306', $hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithHostnameWithoutPort(): void
    {
        $hostInfo = $this->getHostInfo('db.example.com');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME, $hostInfo->getAddressType());
        self::assertSame('db.example.com', $hostInfo->getHostName());
        self::assertNull($hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6Address(): void
    {
        $hostInfo = $this->getHostInfo('[2001:db8::1]:3306');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('2001:db8::1', $hostInfo->getHostName());
        self::assertSame('3306', $hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithoutPort(): void
    {
        $hostInfo = $this->getHostInfo('2001:db8::1');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('2001:db8::1', $hostInfo->getHostName());
        self::assertNull($hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithZoneId(): void
    {
        $hostInfo = $this->getHostInfo('[fe80::1%eth0]:3306');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('fe80::1%eth0', $hostInfo->getHostName());
        self::assertSame('3306', $hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithZoneIdWithoutPort(): void
    {
        $hostInfo = $this->getHostInfo('fe80::1%eth0');

        self::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        self::assertSame('fe80::1%eth0', $hostInfo->getHostName());
        self::assertNull($hostInfo->getPort());
        self::assertNull($hostInfo->getUnixSocket());
    }

    private function getHostInfo(string $str): Varien_Object
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, $str);
        return $hostInfo;
    }
}
