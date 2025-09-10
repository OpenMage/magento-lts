<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper;

use Generator;

trait ProductTrait
{
    public function provideGetAttributeInputTypes(): Generator
    {
        yield 'null' => [
            2,
            null,
        ];
        yield 'invalid' => [
            0,
            'invalid',
        ];
        yield 'multiselect' => [
            1,
            'multiselect',
        ];
        yield 'boolean' => [
            1,
            'boolean',
        ];
    }

    public function provideGetAttributeBackendModelByInputType(): Generator
    {
        yield 'multiselect' => [
            'eav/entity_attribute_backend_array',
            'multiselect',
        ];
    }

    public function provideGetAttributeSourceModelByInputType(): Generator
    {
        yield 'boolean' => [
            'eav/entity_attribute_source_boolean',
            'boolean',
        ];
    }
}
