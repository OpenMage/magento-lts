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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model;

use Generator;
use Mage;
use Mage_Api_Model_User as Subject;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /** @phpstan-ignore property.onlyWritten */
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        Mage::app();
        self::$subject = Mage::getModel('api/user');
    }

    /**
     * @dataProvider provideValidateApiUserData
     * @param array|true $expectedResult
     * @group Mage_Api
     * @group Mage_Api_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods([
                'getUsername',
                'getFirstname',
                'getLastname',
                'getEmail',
                'hasNewApiKey',
                'getNewApiKey',
                'hasApiKey',
                'getApiKey',
                'hasApiKeyConfirmation',
                'getApiKeyConfirmation',
                'userExists',
            ])
            ->getMock();

        $mock->method('getUsername')->willReturn($methods['getUsername']);
        $mock->method('getFirstname')->willReturn($methods['getFirstname']);
        $mock->method('getLastname')->willReturn($methods['getLastname']);
        $mock->method('getEmail')->willReturn($methods['getEmail']);
        $mock->method('hasNewApiKey')->willReturn($methods['hasNewApiKey']);
        $mock->method('getNewApiKey')->willReturn($methods['getNewApiKey']);
        $mock->method('hasApiKey')->willReturn($methods['hasApiKey']);
        $mock->method('getApiKey')->willReturn($methods['getApiKey']);
        $mock->method('hasApiKeyConfirmation')->willReturn($methods['hasApiKeyConfirmation']);
        $mock->method('getApiKeyConfirmation')->willReturn($methods['getApiKeyConfirmation']);
        $mock->method('userExists')->willReturn($methods['userExists']);

        static::assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateApiUserData(): Generator
    {
        $validUser =             [
            'getUsername' => 'validuser',
            'getFirstname' => 'John',
            'getLastname' => 'Doe',
            'getEmail' => 'john.doe@example.com',
            'hasNewApiKey' => false,
            'getNewApiKey' => null,
            'hasApiKey' => true,
            'getApiKey' => 'validapikey123',
            'hasApiKeyConfirmation' => true,
            'getApiKeyConfirmation' => 'validapikey123',
            'userExists' => false,
        ];

        yield 'valid data' => [
            true,
            $validUser,
        ];

        $data = $validUser;
        $data['getUsername'] = '';
        yield 'missing username' => [
            ['User Name is required field.'],
            $data,
        ];

        $data = $validUser;
        $data['getFirstname'] = '';
        yield 'missing firstname' => [
            ['First Name is required field.'],
            $data,
        ];

        $data = $validUser;
        $data['getLastname'] = '';
        yield 'missing lastname' => [
            ['Last Name is required field.'],
            $data,
        ];

        $data = $validUser;
        $data['getEmail'] = '';
        yield 'missing email' => [
            ['Please enter a valid email.'],
            $data,
        ];

        $data = $validUser;
        $data['getEmail'] = 'invalid-email';
        yield 'invalid email' => [
            ['Please enter a valid email.'],
            $data,
        ];

        $data = $validUser;
        $data['getApiKey'] = '';
        yield 'missing api key' => [
            [
                'Api Key must be at least of 7 characters.',
                'Api Key confirmation must be same as Api Key.',
            ],
            $data,
        ];

        $data = $validUser;
        $data['getApiKeyConfirmation'] = '';
        yield 'missing api confirm key' => [
            ['Api Key confirmation must be same as Api Key.'],
            $data,
        ];

        $data = $validUser;
        $apiKey = '1234567';
        $data['getApiKey'] = $apiKey;
        $data['getApiKeyConfirmation'] = $apiKey;
        yield 'numeric only api key' => [
            ['Api Key must include both numeric and alphabetic characters.'],
            $data,
        ];

        $data = $validUser;
        $apiKey = 'abcdefg';
        $data['getApiKey'] = $apiKey;
        $data['getApiKeyConfirmation'] = $apiKey;
        yield 'string only api key' => [
            ['Api Key must include both numeric and alphabetic characters.'],
            $data,
        ];

        $data = $validUser;
        $data['userExists'] = true;
        yield 'user exists' => [
            ['A user with the same user name or email already exists.'],
            $data,
        ];
    }
}
