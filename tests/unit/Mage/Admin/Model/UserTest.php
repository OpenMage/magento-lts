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

use Generator;
use Mage;
use Mage_Admin_Model_User as Subject;
use Mage_Core_Exception;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
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
        $defaultMethods = ['loadByUsername' => null];
        $methods = array_merge($defaultMethods, $methods);

        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(array_keys($methods))
            ->getMock();

        $mock->method('loadByUsername')->willReturnSelf();
        foreach ($methods as $key => $value) {
            $mock->method($key)->willReturn($value);
        }

        try {
            static::assertSame($expectedResult, $mock->authenticate($methods['getUsername'], $methods['getPassword']));
        } catch (Mage_Core_Exception $exception) {
            static::assertSame($expectedResult, $exception->getMessage());
        }
    }

    public function provideAuthenticateData(): Generator
    {
        $validData = [
            'getId'       => '999',
            'getUsername' => 'new',
            'getPassword' => 'veryl0ngpassw0rd',
            'getIsActive' => '1',
            'validatePasswordHash' => true,
            'hasAssigned2Role' => true,
        ];

        yield 'pass' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['getUsername'] = 'admin';
        yield 'fail #0 account exists' => [
            'User Name already exists.',
            $data,
        ];

        $data = $validData;
        $data['getIsActive'] = '0';
        yield 'fail #1 inactive' => [
            'This account is inactive.',
            $data,
        ];

        $data = $validData;
        $data['validatePasswordHash'] = false;
        yield 'fail #2 invalid hash' => [
            false,
            $data,
        ];

        $data = $validData;
        $data['hasAssigned2Role'] = false;
        yield 'fail #3 no role assigned' => [
            'Access denied.',
            $data,
        ];
    }

    /**
     * @dataProvider provideValidateAdminUserData
     * @param array|true $expectedResult
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(array_keys($methods))
            ->getMock();

        foreach ($methods as $key => $value) {
            $mock->method($key)->willReturn($value);
        }

        static::assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateAdminUserData(): Generator
    {
        yield 'fail #1' => [
            [
                0 => 'User Name is required field.',
                1 => 'First Name is required field.',
                2 => 'Last Name is required field.',
                3 => 'Please enter a valid email.',
                4 => 'Password must be at least of 14 characters.',
                5 => 'Password must include both numeric and alphabetic characters.',
            ],
            [
                'hasNewPassword' => true,
                'getNewPassword' => '123',
                'hasPassword' => false,
                'getPassword' => '456',
            ],
        ];
        yield 'fails #2' => [
            [
                0 => 'User Name is required field.',
                1 => 'First Name is required field.',
                2 => 'Last Name is required field.',
                3 => 'Please enter a valid email.',
                4 => 'Password must be at least of 14 characters.',
                5 => 'Password must include both numeric and alphabetic characters.',
            ],
            [
                'hasNewPassword' => false,
                'getNewPassword' => '123',
                'hasPassword' => true,
                'getPassword' => '456',
            ],
        ];
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
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(array_keys($methods))
            ->getMock();

        foreach ($methods as $key => $value) {
            $mock->method($key)->willReturn($value);
        }

        static::assertSame($expectedResult, $mock->isResetPasswordLinkTokenExpired());
    }

    public function provideIsResetPasswordLinkTokenExpiredData(): Generator
    {
        yield '#1' => [
            true,
            [
                'getRpToken'       => '',
                'getRpTokenCreatedAt' => '',
            ],
        ];
        yield '#2' => [
            true,
            [
                'getRpToken'       => '1',
                'getRpTokenCreatedAt' => '0',
            ],
        ];
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
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getStoreConfigAsInt'])
            ->getMock();
        $mock->method('getStoreConfigAsInt')->willReturn(10);

        static::assertSame(14, $mock->getMinAdminPasswordLength());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testSendAdminNotification(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getUserCreateAdditionalEmail'])
            ->getMock();
        $mock->method('getUserCreateAdditionalEmail')->willReturn(['test@example.com']);

        static::assertInstanceOf(Subject::class, $mock->sendAdminNotification(self::$subject));
    }
}
