<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Model;

use Generator;

trait ConsumerTrait
{
    public static function provideValidateData(): Generator
    {
        $validData = [
            'setKey'    => str_repeat('x', 32),
            'setSecret' => str_repeat('x', 32),
        ];

        $error = 'This value should have exactly 32 characters.';

        yield 'valid' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['setKey'] = str_repeat('x', 3);
        yield 'invalid to short key' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setKey'] = str_repeat('x', 33);
        yield 'invalid to long key' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 3);
        yield 'invalid to short secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 33);
        yield 'invalid to long secret' => [
            $error,
            $data,
        ];
    }
}
