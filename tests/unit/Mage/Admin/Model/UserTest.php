<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Override;
use Mage;
use Mage_Admin_Model_Resource_User_Collection;
use Mage_Admin_Model_Roles;
use Mage_Admin_Model_User as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\UserTrait;

/**
 * @phpstan-import-type AuthenticateData from UserTrait
 * @phpstan-import-type AuthenticateMethods from UserTrait
 */
final class UserTest extends OpenMageTest
{
    use UserTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/user');
    }

    /**
     * @group Model
     */
    public function testSaveExtra(): void
    {
        $data = [];
        self::assertInstanceOf(Subject::class, self::$subject->saveExtra($data));
    }

    /**
     * @group Model
     */
    public function testSaveRelations(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->saveRelations());
    }

    /**
     * @group Model
     */
    public function testGetRoles(): void
    {
        self::assertIsArray(self::$subject->getRoles());
    }

    /**
     * @group Model
     */
    public function testGetRole(): void
    {
        self::assertInstanceOf(Mage_Admin_Model_Roles::class, self::$subject->getRole());
    }

    /**
     * @group Model
     */
    public function testDeleteFromRole(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->deleteFromRole());
    }

    /**
     * @group Model
     */
    public function testRoleUserExists(): void
    {
        self::assertIsBool(self::$subject->roleUserExists());
    }

    /**
     * @group Model
     */
    public function testAdd(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->add());
    }

    /**
     * @group Model
     */
    public function testUserExists(): void
    {
        self::assertIsBool(self::$subject->userExists());
    }

    /**
     * @group Model
     */
    public function testGetCollection(): void
    {
        self::assertInstanceOf(Mage_Admin_Model_Resource_User_Collection::class, self::$subject->getCollection());
    }

    /**
     * @group Model
     */
    public function testSendNewPasswordEmail(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->sendNewPasswordEmail());
    }

    /**
     * @group Model
     */
    public function testGetUserId(): void
    {
        self::assertNull(self::$subject->getUserId());

        self::$subject->setUserId(1);
        self::assertIsInt(self::$subject->getUserId());
    }

    /**
     * @group Model
     */
    public function testGetAclRole(): void
    {
        self::assertStringStartsWith('U', self::$subject->getAclRole());
    }

    /**
     * @phpstan-param AuthenticateData $data
     * @phpstan-param AuthenticateMethods $methods
     * @dataProvider provideAuthenticateData
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testAuthenticate(bool|string $expectedResult, array $data, array $methods): void
    {
        $defaultMethods = ['loadByUsername', 'save'];
        $mock = $this->createPartialMock(Subject::class, array_merge($defaultMethods, array_keys($methods)));
        $mock->setData($data);
        foreach ($defaultMethods as $method) {
            $mock->method($method)->willReturnSelf();
        }

        foreach ($methods as $method => $result) {
            $mock->method($method)->willReturn($result);
        }

        self::assertInstanceOf(Subject::class, $mock);

        try {
            self::assertSame($expectedResult, $mock->authenticate($data['username'], $data['password']));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expectedResult, $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    public function testHasAvailableResources()
    {
        self::assertIsBool(self::$subject->hasAvailableResources());
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testFindFirstAvailableMenu()
    {
        self::assertIsString(self::$subject->findFirstAvailableMenu());
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetStartupPageUrl()
    {
        self::assertIsString(self::$subject->getStartupPageUrl());
    }

    /**
     * @dataProvider provideValidateAdminUserData
     * @param array|true            $expectedResult
     * @param array<string, string> $data
     * @group Model
     */
    public function testValidate(array|bool $expectedResult, array $data): void
    {
        self::$subject->setData($data);
        self::assertSame($expectedResult, self::$subject->validate());
    }

    /**
     * @group Model
     */
    public function testValidateCurrentPassword(): void
    {
        self::assertIsArray(self::$subject->validateCurrentPassword(''));
        self::assertIsArray(self::$subject->validateCurrentPassword('123'));
    }

    /**
     * @group Model
     */
    public function testValidatePasswordHash(): void
    {
        self::assertIsBool(self::$subject->validatePasswordHash('a', 'b'));
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testLogin(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->login('a', 'b'));
    }


    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testReload(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->reload());
    }

    /**
     * @group Model
     */
    public function testLoadByUsername(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->loadByUsername('invalid-user'));
    }

    /**
     * @group Model
     */
    public function testHasAssigned2Role(): void
    {
        self::assertIsArray(self::$subject->hasAssigned2Role(1));
    }

    /**
     * @group Model
     */
    public function testChangeResetPasswordLinkToken(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->changeResetPasswordLinkToken('123'));
    }

    /**
     * @dataProvider provideIsResetPasswordLinkTokenExpiredData
     * @param array<string, string> $data
     * @group Model
     */
    public function testIsResetPasswordLinkTokenExpired(bool $expectedResult, array $data): void
    {
        self::$subject->setData($data);
        self::assertSame($expectedResult, self::$subject->isResetPasswordLinkTokenExpired());
    }

    /**
     * @group Model
     */
    public function testSendPasswordResetConfirmationEmail(): void
    {
        Mage::app()->getStore()->setConfig('system/smtp/disable', '1');
        self::assertInstanceOf(Subject::class, self::$subject->sendPasswordResetConfirmationEmail());
    }

    /**
     * @group Model
     */
    public function testCleanPasswordsValidationData(): void
    {
        self::$subject->setData('password', 'test123');
        self::$subject->setData('current_password', 'current123');
        self::$subject->setData('new_password', 'new123');
        self::$subject->setData('password_confirmation', 'confirm123');

        self::$subject->cleanPasswordsValidationData();

        self::assertNull(self::$subject->getDataByKey('password'));
        self::assertNull(self::$subject->getDataByKey('current_password'));
        self::assertNull(self::$subject->getDataByKey('new_password'));
        self::assertNull(self::$subject->getDataByKey('password_confirmation'));
    }

    /**
     * @group Model
     */
    public function testSendAdminNotification(): void
    {
        Mage::app()->getStore()->setConfig('system/smtp/disable', '1');
        $methods = ['getUserCreateAdditionalEmail' => ['test@example.com']];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertInstanceOf(Subject::class, $mock->sendAdminNotification(self::$subject));
    }

    /**
     * @group Model
     */
    public function testGetUserCreateAdditionalEmail(): void
    {
        self::assertSame([0 => ''], self::$subject->getUserCreateAdditionalEmail());
    }

    /**
     * @group Model
     */
    public function testGetMinAdminPasswordLength(): void
    {
        self::assertSame(14, self::$subject->getMinAdminPasswordLength());
    }
}
