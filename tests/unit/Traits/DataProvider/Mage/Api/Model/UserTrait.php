<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model;

use Generator;

trait UserTrait
{
    public static function provideValidateApiUserData(): Generator
    {
        $errorAlphaNumeric = 'Api Key must include both numeric and alphabetic characters.';
        $errorIdentical = 'Api Key confirmation must be same as Api Key.';
        $errorInvalidEmail = 'Please enter a valid email.';
        $errorLength = 'Api Key must be at least of 7 characters.';

        $validUser = [
            'username' => 'validuser',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'new_api_key' => null,
            'api_key' => 'validapikey123',
            'api_key_confirmation' => 'validapikey123',
            'userExists' => false,
        ];

        yield 'valid data' => [
            true,
            $validUser,
            false,
        ];

        $data = $validUser;
        $data['username'] = '';
        yield 'missing username' => [
            ['User Name is required field.'],
            $data,
            false,
        ];

        $data = $validUser;
        $data['firstname'] = '';
        yield 'missing firstname' => [
            ['First Name is required field.'],
            $data,
            false,
        ];

        $data = $validUser;
        $data['lastname'] = '';
        yield 'missing lastname' => [
            ['Last Name is required field.'],
            $data,
            false,
        ];

        $data = $validUser;
        $data['email'] = '';
        yield 'missing email' => [
            [$errorInvalidEmail],
            $data,
            false,
        ];

        $data = $validUser;
        $data['email'] = 'invalid-email';
        yield 'invalid email' => [
            [$errorInvalidEmail],
            $data,
            false,
        ];

        $data = $validUser;
        $data['api_key'] = '';
        yield 'missing api key' => [
            [
                $errorLength,
                $errorIdentical,
            ],
            $data,
            false,
        ];

        $data = $validUser;
        $data['api_key_confirmation'] = '';
        yield 'missing api confirm key' => [
            [$errorIdentical],
            $data,
            false,
        ];

        $data = $validUser;
        $apiKey = '1234567';
        $data['api_key'] = $apiKey;
        $data['api_key_confirmation'] = $apiKey;
        yield 'numeric only api key' => [
            [$errorAlphaNumeric],
            $data,
            false,
        ];

        $data = $validUser;
        $apiKey = 'abcdefg';
        $data['api_key'] = $apiKey;
        $data['api_key_confirmation'] = $apiKey;
        yield 'string only api key' => [
            [$errorAlphaNumeric],
            $data,
            false,
        ];

        $data = $validUser;
        yield 'user exists' => [
            ['A user with the same user name or email already exists.'],
            $data,
            true,
        ];
    }
}
