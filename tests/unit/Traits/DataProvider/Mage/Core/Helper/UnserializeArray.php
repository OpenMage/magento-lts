<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;

trait UnserializeArray
{
    public function provideUnserialize(): Generator
    {
        $errorMessage = 'Error unserializing data.';

        yield 'null' => [
            $errorMessage,
            null,
        ];
        yield 'empty string' => [
            $errorMessage,
            '',
        ];
        yield 'random string' => [
            $errorMessage,
            'abc',
        ];
        yield 'valid' => [
            ['key' => 'value'],
            'a:1:{s:3:"key";s:5:"value";}',
        ];
    }
}
