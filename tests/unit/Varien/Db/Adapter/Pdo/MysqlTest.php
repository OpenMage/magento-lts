<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $this->adapter = $this->getMockBuilder(Varien_Db_Adapter_Pdo_Mysql::class)
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_UNIX_SOCKET);
        $this->assertEquals($hostInfo->getUnixSocket(), $fakeSocket);
        $this->assertNull($hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS);
        $this->assertEquals('192.168.1.1', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV4_ADDRESS);
        $this->assertEquals('192.168.1.1', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME);
        $this->assertEquals('db.example.com', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_HOSTNAME);
        $this->assertEquals('db.example.com', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('2001:db8::1', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('2001:db8::1', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('fe80::1%eth0', $hostInfo->getHostName());
        $this->assertEquals('3306', $hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
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

        $this->assertEquals($hostInfo->getAddressType(), Varien_Db_Adapter_Pdo_Mysql::ADDRESS_TYPE_IPV6_ADDRESS);
        $this->assertEquals('fe80::1%eth0', $hostInfo->getHostName());
        $this->assertNull($hostInfo->getPort());
        $this->assertNull($hostInfo->getUnixSocket());
    }
}
