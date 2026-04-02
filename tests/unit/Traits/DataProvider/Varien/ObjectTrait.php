<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Varien;

use Generator;
use stdClass;
use Varien_Object;

trait ObjectTrait
{
    public function provideGetDataData(): Generator
    {
        yield 'null_key' => [
            null,
            'null_key',
            ['null_value'],
            null,
        ];
        yield 'empty_key' => [
            ['empty_key' => ['empty_value']],
            'empty_key',
            ['empty_value'],
            '',
        ];
        yield 'string' => [
            'value',
            'string',
            'value',
            'string',
        ];
        yield 'int' => [
            1,
            'int',
            1,
            'int',
        ];
        yield 'numeric' => [
            '1',
            'numeric',
            '1',
            'numeric',
        ];
        yield 'array' => [
            ['string', 1],
            'array',
            ['string', 1],
            'array',
        ];
        yield 'array_index_int' => [
            'string',
            'array_index_int',
            ['string', 1],
            'array_index_int',
            0,
        ];
        yield 'array_index_int_invalid' => [
            null,
            'array_index_int_invalid',
            ['string', 1],
            'array_index_int_invalid',
            999,
        ];
        yield 'array_index_string' => [
            1,
            'array_index_string',
            ['string' => 'string', 'int' => 1],
            'array_index_string',
            'int',
        ];
        yield 'array_index_string_string' => [
            null,
            'array_index_string_string',
            'some_string',
            'array_index_string_string',
            'not-exists',
        ];
        yield 'array_index_string_varien_object' => [
            [],
            'array_index_string_varien_object',
            new Varien_Object(['array' => []]),
            'array_index_string_varien_object',
            'array',
        ];
        yield 'array_index_string_std_class' => [
            null,
            'array_index_string_std_class',
            new stdClass(),
            'array_index_string_std_class',
            'not-exists',
        ];
        yield 'array_nested' => [
            1,
            'array_nested',
            ['nested' => ['string' => 'string', 'int' => 1]],
            'array_nested/nested/int',
        ];
        yield 'array_nested_invalid_key' => [
            null,
            'array_nested',
            ['nested' => ['string' => 'string', 'int' => 1]],
            'array_nested/nested/invalid_key',
        ];
        yield 'array_nested_empty_key' => [
            null,
            'array_nested',
            ['nested' => ['string' => 'string', 'int' => '']],
            'array_nested/nested/',
        ];
        yield 'array_nested_string' => [
            'some"\n"string',
            'array_nested_string',
            ['nested' => 'some"\n"string'],
            'array_nested_string/nested',
        ];
        yield 'array_nested_varien_object' => [
            null,
            'array_nested_varien_object',
            new Varien_Object(),
            'array_nested_varien_object/nested',
        ];
        yield 'array_nested_std_class' => [
            null,
            'array_nested_std_class',
            new stdClass(),
            'array_nested_std_class/nested',
        ];
        yield 'array_nested_key_not_exists' => [
            null,
            'array_nested_key_not_exists',
            ['nested' => ['string' => 'string', 'int' => 1]],
            'array_nested_key_not_exists_test/nested/int',
        ];
    }

    public function provideToStringData(): Generator
    {
        yield 'no format' => [
            '0, one, two, three',
            '',
        ];
        yield 'valid' => [
            '0 one two',
            '{{string0}} {{string1}} {{string2}}',
        ];
        yield 'invalid' => [
            'three  0',
            '{{string3}} {{string_not_exists}} {{string0}}',
        ];
    }
}
