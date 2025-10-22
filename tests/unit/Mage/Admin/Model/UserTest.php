<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_User as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\UserTrait;

final class UserTest extends OpenMageTest
{
    use UserTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/user');
    }

    /**
     * @dataProvider provideAuthenticateData
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testAuthenticate(bool|string $expectedResult, array $methods): void
    {
        $defaultMethods = ['loadByUsername' => self::WILL_RETURN_SELF];
        $methods = array_merge($defaultMethods, $methods);
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);

        try {
            self::assertSame($expectedResult, $mock->authenticate($methods['getUsername'], $methods['getPassword']));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expectedResult, $mageCoreException->getMessage());
        }
    }

    /**
     * @dataProvider provideValidateAdminUserData
     * @param array|true $expectedResult
     * @group Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
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
     * @group Model
     */
    public function testIsResetPasswordLinkTokenExpired(bool $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->isResetPasswordLinkTokenExpired());
    }

    /**
     * @group Model
     */
    public function testSendPasswordResetConfirmationEmail(): void
    {
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

        self::assertNull(self::$subject->getData('password'));
        self::assertNull(self::$subject->getData('current_password'));
        self::assertNull(self::$subject->getData('new_password'));
        self::assertNull(self::$subject->getData('password_confirmation'));
    }

    /**
     * @group Model
     */
    public function testGetMinAdminPasswordLength(): void
    {
        $methods = ['getStoreConfigAsInt' => 10];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame(14, $mock->getMinAdminPasswordLength());
    }

    /**
     * @group Model
     */
    public function testSendAdminNotification(): void
    {
        $methods = ['getUserCreateAdditionalEmail' => ['test@example.com']];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertInstanceOf(Subject::class, $mock->sendAdminNotification(self::$subject));
    }
}
