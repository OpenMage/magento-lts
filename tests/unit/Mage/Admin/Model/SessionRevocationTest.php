<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Iterator;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Acl_Role_Group;
use Mage_Admin_Model_Acl_Role_User;
use Mage_Admin_Model_Observer;
use Mage_Admin_Model_Resource_Roles;
use Mage_Admin_Model_Resource_Rules;
use Mage_Admin_Model_Resource_User;
use Mage_Admin_Model_Roles;
use Mage_Admin_Model_Rules;
use Mage_Admin_Model_Session;
use Mage_Admin_Model_User;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Varien_Db_Adapter_Pdo_Mysql;
use Zend_Db_Expr;

/**
 * Revoking an admin user's access must reach the sessions the user already has:
 * deactivation and role removal end them, and permission changes reach every one of them.
 */
final class SessionRevocationTest extends OpenMageTest
{
    /**
     * Every session of the user rebuilds its ACL after a change, not only the first one.
     *
     * @group Model
     */
    public function testEverySessionReloadsAfterAclVersionChange(): void
    {
        $resource = $this->createMock(Mage_Admin_Model_Resource_User::class);
        $resource->expects(self::never())->method('takeReloadAclFlag');
        $resource->expects(self::never())->method('bumpAclVersion');

        $first = $this->createSession(5, 1);
        $second = $this->createSession(5, 1);

        $first->refreshAcl($this->createUser(6, 0, $resource));
        $second->refreshAcl($this->createUser(6, 0, $resource));

        self::assertSame(6, $first->getAclVersion());
        self::assertSame(6, $second->getAclVersion());
    }

    /**
     * @dataProvider provideRefreshAclData
     * @group Model
     */
    public function testRefreshAcl(?int $storedVersion, bool $hasAcl, int $userVersion, int $expectedLoads): void
    {
        $resource = $this->createMock(Mage_Admin_Model_Resource_User::class);
        $resource->expects(self::never())->method('takeReloadAclFlag');

        $session = $this->createSession($storedVersion, $expectedLoads, $hasAcl);
        $session->refreshAcl($this->createUser($userVersion, 0, $resource));

        self::assertSame($userVersion, $session->getAclVersion());
    }

    /**
     * @return Iterator<string, array{(null|int), bool, int, int}>
     */
    public static function provideRefreshAclData(): Iterator
    {
        yield 'unchanged version keeps the ACL' => [7, true, 7, 0];
        yield 'never changed user keeps the ACL' => [0, true, 0, 0];
        yield 'changed version reloads' => [7, true, 8, 1];
        yield 'session from before the upgrade reloads once' => [null, true, 0, 1];
        yield 'session without ACL loads it' => [7, false, 7, 1];
    }

    /**
     * A reload request in reload_acl_flag moves the user to a new version, whichever session takes it.
     *
     * @dataProvider provideLegacyReloadFlagData
     * @group Model
     */
    public function testLegacyReloadFlagBecomesNewVersion(int $storedVersion, bool $takesRequest): void
    {
        $resource = $this->createMock(Mage_Admin_Model_Resource_User::class);
        $resource->expects(self::once())->method('takeReloadAclFlag')->willReturn($takesRequest);
        $resource->expects(self::once())->method('getAclVersion')->willReturn($storedVersion + 1);
        $resource->expects(self::never())->method('saveReloadAclFlag');

        $user = $this->createUser($storedVersion, 1, $resource);
        $session = $this->createSession($storedVersion, 1);
        $session->refreshAcl($user);
        // a second refresh in the same request does not take the request again
        $session->refreshAcl($user);

        self::assertSame($storedVersion + 1, $session->getAclVersion());
        self::assertSame(0, $user->getReloadAclFlag());
    }

    /**
     * @return Iterator<string, array{int, bool}>
     */
    public static function provideLegacyReloadFlagData(): Iterator
    {
        yield 'this session takes the request' => [8, true];
        yield 'another session took the request' => [8, false];
        yield 'session that built the first version' => [2, true];
        yield 'user that never changed' => [0, true];
    }

    /**
     * @group Model
     */
    public function testSaveReloadAclFlagBumpsTheVersion(): void
    {
        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_User::class, ['bumpAclVersion', 'cleanMenuCache', '_getWriteAdapter']);
        $resource->expects(self::once())->method('bumpAclVersion')->with([42])->willReturn(1);
        $resource->expects(self::once())->method('cleanMenuCache');
        $resource->expects(self::never())->method('_getWriteAdapter');

        $resource->saveReloadAclFlag($this->createUserModel(42), 1);
    }

    /**
     * Clearing the flag keeps its meaning: it drops a reload request without changing the version.
     *
     * @group Model
     */
    public function testSaveReloadAclFlagClearsTheRequest(): void
    {
        $adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);
        $adapter->expects(self::once())->method('update')
            ->with('admin_user', ['reload_acl_flag' => 0], ['user_id = ?' => 42])
            ->willReturn(1);

        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_User::class, ['bumpAclVersion', '_getWriteAdapter', 'getMainTable']);
        $resource->expects(self::never())->method('bumpAclVersion');
        $resource->method('_getWriteAdapter')->willReturn($adapter);
        $resource->method('getMainTable')->willReturn('admin_user');

        $resource->saveReloadAclFlag($this->createUserModel(42), 0);
    }

    /**
     * @group Model
     */
    public function testBumpAclVersionIsOneAtomicUpdate(): void
    {
        $update = [];
        $resource = $this->createResourceWithUpdate($update, 2);

        self::assertSame(2, $resource->bumpAclVersion(['3', 0, '-1', 5]));

        [$table, $bind, $where] = $update;
        self::assertSame('admin_user', $table);
        self::assertSame(['user_id IN (?)' => [3, 5]], $where);
        self::assertSame(['acl_version'], array_keys($bind));
        self::assertInstanceOf(Zend_Db_Expr::class, $bind['acl_version']);
        self::assertSame('`acl_version` + 1', (string) $bind['acl_version']);
    }

    /**
     * @group Model
     */
    public function testBumpAclVersionWithoutUsersDoesNothing(): void
    {
        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_User::class, ['_getWriteAdapter']);
        $resource->expects(self::never())->method('_getWriteAdapter');

        self::assertSame(0, $resource->bumpAclVersion([]));
    }

    /**
     * Taking the request clears the flag and bumps the version in one statement, only while it is set.
     *
     * @dataProvider provideTakeReloadAclFlagData
     * @group Model
     */
    public function testTakeReloadAclFlag(int $updatedRows, bool $expected): void
    {
        $update = [];
        $resource = $this->createResourceWithUpdate($update, $updatedRows);

        self::assertSame($expected, $resource->takeReloadAclFlag($this->createUserModel(42)));

        [$table, $bind, $where] = $update;
        self::assertSame('admin_user', $table);
        self::assertSame(['user_id = ?' => 42, 'reload_acl_flag <> ?' => 0], $where);
        self::assertSame('`acl_version` + 1', (string) $bind['acl_version']);
        self::assertSame(0, $bind['reload_acl_flag']);
    }

    /**
     * @return Iterator<string, array{int, bool}>
     */
    public static function provideTakeReloadAclFlagData(): Iterator
    {
        yield 'request taken' => [1, true];
        yield 'another session took it' => [0, false];
    }

    /**
     * Saving a loaded user never writes the ACL version, nor a reload flag it did not change.
     *
     * @dataProvider providePrepareDataForSaveData
     * @param string[] $expectedKeys
     * @group Model
     */
    public function testPrepareDataForSaveLeavesAclStateAlone(int $userId, bool $changeFlag, array $expectedKeys): void
    {
        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_User::class, ['_prepareDataForTable', 'getMainTable']);
        $resource->method('getMainTable')->willReturn('admin_user');
        $resource->method('_prepareDataForTable')->willReturnCallback(
            static fn(Mage_Admin_Model_User $user): array => $user->getData(),
        );

        $user = $this->createUserModel($userId);
        $user->addData(['username' => 'admin', 'acl_version' => 4, 'reload_acl_flag' => 0]);
        $user->setOrigData();
        if ($changeFlag) {
            $user->setReloadAclFlag(1);
        }

        $data = (new ReflectionMethod($resource, '_prepareDataForSave'))->invoke($resource, $user);

        self::assertSame($expectedKeys, array_keys($data));
    }

    /**
     * @return Iterator<string, array{int, bool, string[]}>
     */
    public static function providePrepareDataForSaveData(): Iterator
    {
        yield 'loaded user' => [42, false, ['user_id', 'username']];
        yield 'loaded user asking for a reload' => [42, true, ['user_id', 'username', 'reload_acl_flag']];
        yield 'new user' => [0, false, ['user_id', 'username', 'acl_version', 'reload_acl_flag']];
    }

    /**
     * @group Model
     */
    public function testDeleteFromRoleReloadsUserAcl(): void
    {
        $log = [];
        $adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);
        $adapter->method('delete')->willReturnCallback(static function () use (&$log): int {
            $log[] = 'delete';
            return 1;
        });

        $resource = $this->createPartialMock(
            Mage_Admin_Model_Resource_User::class,
            ['_getWriteAdapter', 'getTable', 'saveReloadAclFlag'],
        );
        $resource->method('_getWriteAdapter')->willReturn($adapter);
        $resource->method('getTable')->willReturn('admin_role');
        $resource->expects(self::once())->method('saveReloadAclFlag')->willReturnCallback(
            static function (Mage_Admin_Model_User $user, int $flag) use (&$log, $resource): Mage_Admin_Model_Resource_User {
                $log[] = 'reload ' . $user->getId() . ':' . $flag;
                return $resource;
            },
        );

        $user = (new Mage_Admin_Model_User())->setIdFieldName('user_id')->setUserId(42)->setRoleId(3);
        $resource->deleteFromRole($user);

        self::assertSame(['delete', 'reload 42:1'], $log);
    }

    /**
     * Members are read before their rows are deleted, and their ACL is reloaded.
     *
     * @group Model
     */
    public function testRoleDeleteReloadsMembersAcl(): void
    {
        $log = [];
        $adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);
        $adapter->method('delete')->willReturnCallback(static function (string $table) use (&$log): int {
            $log[] = 'delete ' . $table;
            return 1;
        });

        $userResource = $this->createUserResourceLogging($log);

        $resource = $this->createPartialMock(
            Mage_Admin_Model_Resource_Roles::class,
            ['getRoleUsers', '_getWriteAdapter', 'getMainTable', '_getUserResource'],
        );
        $resource->method('getRoleUsers')->willReturnCallback(static function () use (&$log): array {
            $log[] = 'read members';
            return ['4', '5'];
        });
        $resource->method('_getWriteAdapter')->willReturn($adapter);
        $resource->method('getMainTable')->willReturn('admin_role');
        $resource->method('_getUserResource')->willReturn($userResource);
        (new ReflectionProperty(Mage_Admin_Model_Resource_Roles::class, '_ruleTable'))->setValue($resource, 'admin_rule');

        $afterDelete = new ReflectionMethod(Mage_Admin_Model_Resource_Roles::class, '_afterDelete');
        $afterDelete->invoke($resource, (new Mage_Admin_Model_Roles())->setIdFieldName('role_id')->setId(3));

        self::assertSame(['read members', 'delete admin_role', 'delete admin_rule', 'bump 4,5', 'clean menu'], $log);
    }

    /**
     * Saving a role bumps its members once and cleans the menu cache once.
     *
     * @group Model
     */
    public function testRoleSaveReloadsMembersAcl(): void
    {
        $log = [];
        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_Roles::class, ['getRoleUsers', '_getUserResource']);
        $resource->method('getRoleUsers')->willReturn(['4']);
        $resource->method('_getUserResource')->willReturn($this->createUserResourceLogging($log));

        $afterSave = new ReflectionMethod(Mage_Admin_Model_Resource_Roles::class, '_afterSave');
        $afterSave->invoke($resource, (new Mage_Admin_Model_Roles())->setIdFieldName('role_id')->setId(3));

        self::assertSame(['bump 4', 'clean menu'], $log);
    }

    /**
     * The members' new ACL version is committed with the role's new rules, or rolled back with them.
     *
     * @dataProvider provideSaveRelData
     * @param string[] $expectedLog
     * @group Model
     */
    public function testSaveRelCommitsMembersAclVersionWithRules(bool $fails, array $expectedLog): void
    {
        $log = [];
        $adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);
        $adapter->method('beginTransaction')->willReturnCallback(static function () use (&$log, $adapter): Varien_Db_Adapter_Pdo_Mysql {
            $log[] = 'begin';
            return $adapter;
        });
        $adapter->method('delete')->willReturnCallback(static function () use (&$log, $fails): int {
            if ($fails) {
                throw new RuntimeException('lost connection');
            }

            $log[] = 'delete rules';
            return 1;
        });
        $adapter->method('commit')->willReturnCallback(static function () use (&$log, $adapter): Varien_Db_Adapter_Pdo_Mysql {
            $log[] = 'commit';
            return $adapter;
        });
        $adapter->method('rollBack')->willReturnCallback(static function () use (&$log, $adapter): Varien_Db_Adapter_Pdo_Mysql {
            $log[] = 'roll back';
            return $adapter;
        });

        $resource = $this->createPartialMock(
            Mage_Admin_Model_Resource_Rules::class,
            ['_getWriteAdapter', 'getMainTable', '_reloadRoleUsersAcl', '_getUserResource'],
        );
        $resource->method('_getWriteAdapter')->willReturn($adapter);
        $resource->method('getMainTable')->willReturn('admin_rule');
        $resource->method('_getUserResource')->willReturn($this->createUserResourceLogging($log));
        $resource->method('_reloadRoleUsersAcl')->with(3)->willReturnCallback(
            static function () use (&$log): bool {
                $log[] = 'bump members';
                return true;
            },
        );

        $resource->saveRel((new Mage_Admin_Model_Rules())->setRoleId(3)->setResources([]));

        self::assertSame($expectedLog, $log);
    }

    /**
     * @return Iterator<string, array{bool, string[]}>
     */
    public static function provideSaveRelData(): Iterator
    {
        yield 'saved' => [false, ['begin', 'delete rules', 'bump members', 'commit', 'clean menu']];
        yield 'rolled back' => [true, ['begin', 'roll back']];
    }

    /**
     * @dataProvider provideSessionUserAllowedData
     * @group Model
     */
    public function testSessionUserAllowed(null|int|string $isActive, bool $hasRole, bool $expected): void
    {
        $acl = new Mage_Admin_Model_Acl();
        $acl->addRole(new Mage_Admin_Model_Acl_Role_Group('G1'));
        if ($hasRole) {
            $acl->addRole(new Mage_Admin_Model_Acl_Role_User('U42'), 'G1');
        }

        $session = $this->createPartialMock(Mage_Admin_Model_Session::class, ['refreshAcl']);
        $session->method('refreshAcl')->willReturnSelf();
        $session->setAcl($acl);

        $user = (new Mage_Admin_Model_User())->setIdFieldName('user_id')->setUserId(42)->setIsActive($isActive);

        $method = new ReflectionMethod(Mage_Admin_Model_Observer::class, '_isSessionUserAllowed');
        self::assertSame($expected, $method->invoke(new Mage_Admin_Model_Observer(), $session, $user));
    }

    /**
     * @return Iterator<string, array{(null|int|string), bool, bool}>
     */
    public static function provideSessionUserAllowedData(): Iterator
    {
        yield 'active user with role' => ['1', true, true];
        yield 'active user with role (int)' => [1, true, true];
        yield 'inactive user' => ['0', true, false];
        yield 'inactive user (int)' => [0, true, false];
        yield 'missing status' => [null, true, false];
        yield 'active user without role' => ['1', false, false];
    }

    /**
     * @return Mage_Admin_Model_Session&MockObject
     */
    private function createSession(?int $version, int $expectedLoads, bool $hasAcl = true): MockObject
    {
        $session = $this->createPartialMock(Mage_Admin_Model_Session::class, ['_loadAcl']);
        $session->expects(self::exactly($expectedLoads))->method('_loadAcl')->willReturn(new Mage_Admin_Model_Acl());
        if ($hasAcl) {
            $session->setAcl(new Mage_Admin_Model_Acl());
        }

        if ($version !== null) {
            $session->setAclVersion($version);
        }

        return $session;
    }

    /**
     * @return Mage_Admin_Model_User&MockObject
     */
    private function createUser(int $version, int $reloadFlag, Mage_Admin_Model_Resource_User $resource): MockObject
    {
        $user = $this->createPartialMock(Mage_Admin_Model_User::class, ['getResource']);
        $user->method('getResource')->willReturn($resource);
        $user->setIdFieldName('user_id')->setId(42)->setAclVersion($version)->setReloadAclFlag($reloadFlag);

        return $user;
    }

    private function createUserModel(int $userId): Mage_Admin_Model_User
    {
        return (new Mage_Admin_Model_User())->setIdFieldName('user_id')->setId($userId);
    }

    /**
     * A user resource whose write adapter records its one update
     *
     * @param  array<mixed>                              $update table, bind and where of the update
     * @return Mage_Admin_Model_Resource_User&MockObject
     */
    private function createResourceWithUpdate(array &$update, int $updatedRows): MockObject
    {
        $adapter = $this->createMock(Varien_Db_Adapter_Pdo_Mysql::class);
        $adapter->method('quoteIdentifier')->willReturnCallback(static fn(string $name): string => '`' . $name . '`');
        $adapter->expects(self::once())->method('update')->willReturnCallback(
            static function (string $table, array $bind, array $where) use (&$update, $updatedRows): int {
                $update = [$table, $bind, $where];
                return $updatedRows;
            },
        );

        $resource = $this->createPartialMock(Mage_Admin_Model_Resource_User::class, ['_getWriteAdapter', 'getMainTable']);
        $resource->method('_getWriteAdapter')->willReturn($adapter);
        $resource->method('getMainTable')->willReturn('admin_user');

        return $resource;
    }

    /**
     * @param  array<string>                             $log
     * @return Mage_Admin_Model_Resource_User&MockObject
     */
    private function createUserResourceLogging(array &$log): MockObject
    {
        $userResource = $this->createMock(Mage_Admin_Model_Resource_User::class);
        $userResource->method('bumpAclVersion')->willReturnCallback(
            static function (array $userIds) use (&$log): int {
                $log[] = 'bump ' . implode(',', $userIds);
                return count($userIds);
            },
        );
        $userResource->method('cleanMenuCache')->willReturnCallback(static function () use (&$log): void {
            $log[] = 'clean menu';
        });

        return $userResource;
    }
}
