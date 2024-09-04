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
use Mage_Admin_Model_User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var Mage_Admin_Model_User
     */
    public Mage_Admin_Model_User $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('admin/user');
    }

    /**
     * @dataProvider provideValidateData
     * @param array|true $expectedResult
     * @param array $methods
     * @return void
     *
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Mage_Admin_Model_User::class)
            ->setMethods([
                'hasNewPassword',
                'getNewPassword',
                'hasPassword',
                'getPassword',
            ])
            ->getMock();

        $mock->expects($this->any())->method('hasNewPassword')->willReturn($methods['hasNewPassword']);
        $mock->expects($this->any())->method('getNewPassword')->willReturn($methods['getNewPassword']);
        $mock->expects($this->any())->method('hasPassword')->willReturn($methods['hasPassword']);
        $mock->expects($this->any())->method('getPassword')->willReturn($methods['getPassword']);
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        $this->assertEquals($expectedResult, $mock->validate());
    }

    /**
     * @return array<string, array<int, bool|array|string>>
     */
    public function provideValidateData(): array
    {
        return [
            'test_fails_1' => [
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
                ]
            ],
            'test_fails_2' => [
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
                ]
            ],
        ];
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testValidateCurrentPassword(): void
    {
        $this->assertIsArray($this->subject->validateCurrentPassword(''));
        $this->assertIsArray($this->subject->validateCurrentPassword('123'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testLoadByUsername(): void
    {
        $this->assertInstanceOf(Mage_Admin_Model_User::class, $this->subject->loadByUsername('invalid-user'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testChangeResetPasswordLinkToken(): void
    {
        $this->assertInstanceOf(Mage_Admin_Model_User::class, $this->subject->changeResetPasswordLinkToken('123'));
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testIsResetPasswordLinkTokenExpired(): void
    {
        $this->assertIsBool($this->subject->isResetPasswordLinkTokenExpired());
    }

    /**
     * @group Mage_Admin
     * @group Mage_Admin_Model
     */
    public function testSendPasswordResetConfirmationEmail(): void
    {
        $this->assertInstanceOf(Mage_Admin_Model_User::class, $this->subject->sendPasswordResetConfirmationEmail());
    }
}
