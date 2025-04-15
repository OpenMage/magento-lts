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

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_User as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\UserTrait;

class UserTest extends OpenMageTest
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
     * @group Mage_Admin
     * @group Mage_Admin_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testAuthenticate(bool|string $expectedResult, array $methods): void
    {
        $defaultMethods = ['loadByUsername' => self::WILL_RETURN_SELF];
        $methods = array_merge($defaultMethods, $methods);
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);

        try {
            static::assertSame($expectedResult, $mock->authenticate($methods['getUsername'], $methods['getPassword']));
        } catch (Mage_Core_Exception $exception) {
            static::assertSame($expectedResult, $exception->getMessage());
        }
    }

    /**
     * @dataProvider provideValidateAdminUserData
     * @param array|true $expectedResult
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->validate());
    }



    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidateCurrentPassword(): void
    {
        static::assertIsArray(self::$subject->validateCurrentPassword(''));
        static::assertIsArray(self::$subject->validateCurrentPassword('123'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testLoadByUsername(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->loadByUsername('invalid-user'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testHasAssigned2Role(): void
    {
        static::assertIsArray(self::$subject->hasAssigned2Role(1));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testChangeResetPasswordLinkToken(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->changeResetPasswordLinkToken('123'));
    }

    /**
     * @dataProvider provideIsResetPasswordLinkTokenExpiredData
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testIsResetPasswordLinkTokenExpired(bool $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->isResetPasswordLinkTokenExpired());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testSendPasswordResetConfirmationEmail(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->sendPasswordResetConfirmationEmail());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testCleanPasswordsValidationData(): void
    {
        self::$subject->setData('password', 'test123');
        self::$subject->setData('current_password', 'current123');
        self::$subject->setData('new_password', 'new123');
        self::$subject->setData('password_confirmation', 'confirm123');

        self::$subject->cleanPasswordsValidationData();

        static::assertNull(self::$subject->getData('password'));
        static::assertNull(self::$subject->getData('current_password'));
        static::assertNull(self::$subject->getData('new_password'));
        static::assertNull(self::$subject->getData('password_confirmation'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testGetMinAdminPasswordLength(): void
    {
        $methods = ['getStoreConfigAsInt' => 10];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame(14, $mock->getMinAdminPasswordLength());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testSendAdminNotification(): void
    {
        $methods = ['getUserCreateAdditionalEmail' => ['test@example.com']];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertInstanceOf(Subject::class, $mock->sendAdminNotification(self::$subject));
    }
}
