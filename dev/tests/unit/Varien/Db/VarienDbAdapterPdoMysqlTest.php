<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Core\Helper;

use PHPUnit\Framework\TestCase;
use Varien_Db_Adapter_Pdo_Mysql;

class VarienDbAdapterPdoMysqlTest extends TestCase
{
    private Varien_Db_Adapter_Pdo_Mysql $adapter;

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
        $this->adapter = $this->getMockBuilder(Varien_Db_Adapter_Pdo_Mysql::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Call the constructor manually with our config
        $reflectedAdapter = new \ReflectionClass(Varien_Db_Adapter_Pdo_Mysql::class);
        $constructor = $reflectedAdapter->getConstructor();
        $constructor->invoke($this->adapter, $config);
    }

    public function testGetHostInfoWithUnixSocket(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $fakeSocket = '/var/run/mysqld/mysqld.sock';
        $hostInfo = $method->invoke($this->adapter, $fakeSocket);

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_UNIX_SOCKET);
        $this->assertEquals($hostInfo->getUnixSocket(), $fakeSocket);
        $this->assertNull($hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
    }

    public function testGetHostInfoWithIpv4Address(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, '192.168.1.1:3306');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS);
        $this->assertEquals('192.168.1.1', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithIpv4AddressWithoutPort(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, '192.168.1.1');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS);
        $this->assertEquals('192.168.1.1', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithHostname(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, 'db.example.com:3306');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME);
        $this->assertEquals('db.example.com', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithHostnameWithoutPort(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, 'db.example.com');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME);
        $this->assertEquals('db.example.com', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithIpv6Address(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, '[2001:db8::1]:3306');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('2001:db8::1', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithIpv6AddressWithoutPort(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, '2001:db8::1');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('2001:db8::1', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithIpv6AddressWithZoneId(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, '[fe80::1%eth0]:3306');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('fe80::1%eth0', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }

    public function testGetHostInfoWithIpv6AddressWithZoneIdWithoutPort(): void
    {
        $method = new \ReflectionMethod(Varien_Db_Adapter_Pdo_Mysql::class, '_getHostInfo');
        $method->setAccessible(true);

        $hostInfo = $method->invoke($this->adapter, 'fe80::1%eth0');

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('fe80::1%eth0', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }
}
