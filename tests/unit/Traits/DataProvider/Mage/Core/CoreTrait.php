<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core;

use Generator;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\ModulesTrait;

trait CoreTrait
{
    use ModulesTrait;

    public function provideIsOutputEnabled(): Generator
    {
        yield 'null' => [
            true,
            null, #Mage_Adminhtml
        ];

        foreach ($this->provideAllActiveModules() as $module) {
            yield $module => [
                true,
                $module,
            ];
        }

        yield 'Not_Exist' => [
            false,
            'Not_Exist',
        ];
    }

    public function provideGetStoreConfig(): Generator
    {
        yield 'null' => [
            null,
        ];
        yield 'true' => [
            true,
        ];
        yield 'false' => [
            false,
        ];
        yield 'int valid' => [
            1,
        ];
        yield 'int invalid (exception)' => [
            999,
        ];
        yield 'string' => [
            '1',
        ];
    }

    public function provideGetStoreId(): Generator
    {
        yield 'string' => [
            1,
            '1',
        ];
        yield 'int' => [
            1,
            1,
        ];
        yield 'null' => [
            null,
            null,
        ];
    }
}
