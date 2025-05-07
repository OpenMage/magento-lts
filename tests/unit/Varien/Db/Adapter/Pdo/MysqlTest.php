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

class MysqlTest extends TestCase
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
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $fakeSocket = '/var/run/mysqld/mysqld.sock';

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, $fakeSocket);

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_UNIX_SOCKET, $hostInfo->getAddressType());
        static::assertSame($fakeSocket, $hostInfo->getUnixSocket());
        static::assertNull($hostInfo->getHostName());
        static::assertNull($hostInfo->getPort());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv4Address(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, '192.168.1.1:3306');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('192.168.1.1', $hostInfo->getHostName());
        static::assertSame('3306', $hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv4AddressWithoutPort(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, '192.168.1.1');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('192.168.1.1', $hostInfo->getHostName());
        static::assertNull($hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithHostname(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, 'db.example.com:3306');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME, $hostInfo->getAddressType());
        static::assertSame('db.example.com', $hostInfo->getHostName());
        static::assertSame('3306', $hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithHostnameWithoutPort(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, 'db.example.com');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME, $hostInfo->getAddressType());
        static::assertSame('db.example.com', $hostInfo->getHostName());
        static::assertNull($hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6Address(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, '[2001:db8::1]:3306');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('2001:db8::1', $hostInfo->getHostName());
        static::assertSame('3306', $hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithoutPort(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, '2001:db8::1');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('2001:db8::1', $hostInfo->getHostName());
        static::assertNull($hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithZoneId(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, '[fe80::1%eth0]:3306');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('fe80::1%eth0', $hostInfo->getHostName());
        static::assertSame('3306', $hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }

    /**
     * @group Varien_Db
     */
    public function testGetHostInfoWithIpv6AddressWithZoneIdWithoutPort(): void
    {
        $method = new ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        /** @var Varien_Object $hostInfo */
        $hostInfo = $method->invoke($this->adapter, 'fe80::1%eth0');

        static::assertSame(Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS, $hostInfo->getAddressType());
        static::assertSame('fe80::1%eth0', $hostInfo->getHostName());
        static::assertNull($hostInfo->getPort());
        static::assertNull($hostInfo->getUnixSocket());
    }
}
