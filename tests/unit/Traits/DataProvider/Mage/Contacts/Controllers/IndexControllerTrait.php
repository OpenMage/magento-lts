<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Contacts\Controllers;

use Generator;

trait IndexControllerTrait
{
    public function providePostActionData(): Generator
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'comment' => 'Test comment',
        ];

        $error = 'Unable to submit your request. Please, try again later';

        #yield 'valid data' => [
        #    $validData,
        #    true,
        #    null,
        #];

        yield 'invalid form key' => [
            $validData,
            false,
            'Invalid Form Key. Please submit your request again.',
        ];

        $data = $validData;
        $data['name'] = '';
        yield 'missing name' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['email'] = '';
        yield 'missing email' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['email'] = 'invalid-email';
        yield 'invalid email' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['comment'] = '';
        yield 'missing comment' => [
            $data,
            true,
            $error,
        ];
    }
}
