<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model;

use Generator;

trait UserTrait
{
    public function provideValidateApiUserData(): Generator
    {
        $errorAlphaNumeric = 'Api Key must include both numeric and alphabetic characters.';
        $errorIdentical = 'Api Key confirmation must be same as Api Key.';
        $errorInvalidEmail = 'Please enter a valid email.';
        $errorLength = 'Api Key must be at least of 7 characters.';

        $validUser = [
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
            [$errorInvalidEmail],
            $data,
        ];

        $data = $validUser;
        $data['getEmail'] = 'invalid-email';
        yield 'invalid email' => [
            [$errorInvalidEmail],
            $data,
        ];

        $data = $validUser;
        $data['getApiKey'] = '';
        yield 'missing api key' => [
            [
                $errorLength,
                $errorIdentical,
            ],
            $data,
        ];

        $data = $validUser;
        $data['getApiKeyConfirmation'] = '';
        yield 'missing api confirm key' => [
            [$errorIdentical],
            $data,
        ];

        $data = $validUser;
        $apiKey = '1234567';
        $data['getApiKey'] = $apiKey;
        $data['getApiKeyConfirmation'] = $apiKey;
        yield 'numeric only api key' => [
            [$errorAlphaNumeric],
            $data,
        ];

        $data = $validUser;
        $apiKey = 'abcdefg';
        $data['getApiKey'] = $apiKey;
        $data['getApiKeyConfirmation'] = $apiKey;
        yield 'string only api key' => [
            [$errorAlphaNumeric],
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
