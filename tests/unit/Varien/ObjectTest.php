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

namespace OpenMage\Tests\Unit\Varien;

use Generator;
use PHPUnit\Framework\TestCase;
use stdClass;
use Varien_Exception;
use Varien_Object;

class ObjectTest extends TestCase
{
    public Varien_Object $subject;

    public function setUp(): void
    {
        $this->subject = new Varien_Object();
    }

    /**
     * @dataProvider provideGetDataData
     * @param mixed $expectedResult
     * @param string $setKey
     * @param mixed $setValue
     * @param string|int|null $index
     * @group Varien_Object
     */
    public function testGetData($expectedResult, $setKey, $setValue, string $key, $index = null): void
    {
        $this->subject->setData($setKey, $setValue);
        $this->assertSame($expectedResult[__FUNCTION__], $this->subject->getData($key, $index));
    }

    /**
     * @covers Varien_Object::jsonSerialize()
     * @dataProvider provideGetDataData
     * @param mixed $expectedResult
     * @param string $setKey
     * @param mixed $setValue
     * @group Varien_Object
     */
    public function testJsonSerialize($expectedResult, $setKey, $setValue): void
    {
        $this->subject->setData($setKey, $setValue);
        $this->assertSame($expectedResult[__FUNCTION__], $this->subject->jsonSerialize());
    }

    public function provideGetDataData(): Generator
    {
        $key   = 'empty_key';
        $value = ['empty_value'];
        yield $key => [
            [
                'testGetData'       => [$key => $value],
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            ''
        ];

        $key   = 'string';
        $value = 'value';
        yield $key => [
            [
                'testGetData'       => $value,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'string'
        ];

        $key   = 'int';
        $value = 1;
        yield $key => [
            [
                'testGetData'       => $value,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'int'
        ];

        $key   = 'numeric';
        $value = '1';
        yield $key => [
            [
                'testGetData'       => $value,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'numeric'
        ];

        $key   = 'array';
        $value = ['string', 1];
        yield $key => [
            [
                'testGetData'       => $value,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array',
        ];

        $key   = 'array_index_int';
        $value = ['string', 1];
        yield $key => [
            [
                'testGetData'       => 'string',
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_int',
            0,
        ];

        $key   = 'array_index_int_invalid';
        $value = ['string', 1];
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_int_invalid',
            999,
        ];

        $key   = 'array_index_string';
        $value = ['string' => 'string', 'int' => 1];
        yield $key => [
            [
                'testGetData'       => 1,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_string',
            'int',
        ];

        $key   = 'array_index_string_string';
        $value = 'some_string';
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_string_string',
            'not-exists',
        ];

        $key   = 'array_index_string_varien_object';
        $value = new Varien_Object(['array' => []]);
        yield $key => [
            [
                'testGetData'       => [],
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_string_varien_object',
            'array',
        ];

        $key   = 'array_index_string_std_class';
        $value = new stdClass();
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_index_string_std_class',
            'not-exists',
        ];

        $key   = 'array_nested';
        $value = ['nested' => ['string' => 'string', 'int' => 1]];
        yield $key => [
            [
                'testGetData'       => 1,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested/nested/int',
        ];

        $key   = 'array_nested_invalid_key';
        $value = ['nested' => ['string' => 'string', 'int' => 1]];
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested/nested/invalid_key',
        ];

        $key   = 'array_nested';
        $value = ['nested' => ['string' => 'string', 'int' => '']];
        yield 'array_nested_empty_key' => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested/nested/',
        ];

        $key   = 'array_nested_string';
        $value = ['nested' => 'some"\n"string'];
        yield 'array_nested_string' => [
            [
                'testGetData'       => 'some"\n"string',
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested_string/nested',
        ];

        $key   = 'array_nested_varien_object';
        $value = new Varien_Object();
        yield 'array_nested_varien_object' => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested_varien_object/nested',
        ];

        $key   = 'array_nested_std_class';
        $value = new stdClass();
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested_std_class/nested',
        ];

        $key   = 'array_nested_key_not_exists';
        $value = ['nested' => ['string' => 'string', 'int' => 1]];
        yield $key => [
            [
                'testGetData'       => null,
                'testJsonSerialize' => [$key => $value],
            ],
            $key,
            $value,
            'array_nested_key_not_exists_test/nested/int',
        ];
    }

    /**
     * @group Varien_Object
     */
    public function testToString(): void
    {
        $this->subject->setString1('open');
        $this->subject->setString2('mage');
        $this->assertSame('open, mage', $this->subject->toString());
        $this->assertSame('openmage', $this->subject->toString('{{string1}}{{string2}}'));
        $this->assertSame('open', $this->subject->toString('{{string1}}{{string_not_exists}}'));
    }

    /**
     * @group Varien_Object
     */
    public function testGetSetUnsData(): void
    {
        $this->assertTrue($this->subject->isEmpty());
        $this->subject->setABC('abc');
        $this->subject->setData('efg', 'efg');
        $this->subject->set123('123');
        $this->subject->setData('345', '345');
        $this->subject->setKeyAFirst('value_a_first');
        $this->subject->setData('key_a_2nd', 'value_a_2nd');
        $this->subject->setKeyA3rd('value_a_3rd');
        $this->subject->setData('left', 'over');
        $this->assertFalse($this->subject->isEmpty());

        $this->assertSame('abc', $this->subject->getData('a_b_c'));
        $this->assertSame('abc', $this->subject->getABC());
        $this->subject->unsetData('a_b_c');

        $this->assertSame('efg', $this->subject->getData('efg'));
        $this->assertSame('efg', $this->subject->getEfg());
        $this->subject->unsEfg();

        $this->assertSame('123', $this->subject->getData('123'));
        $this->assertSame('123', $this->subject->get123());
        $this->subject->uns123();

        $this->subject->unsetData('345');

        $this->assertSame('value_a_first', $this->subject->getData('key_a_first'));
        $this->assertSame('value_a_first', $this->subject->getKeyAFirst());
        $this->subject->unsetData('key_a_first');

        $this->assertSame('value_a_2nd', $this->subject->getData('key_a_2nd'));
        $this->assertSame('value_a_2nd', $this->subject->getKeyA_2nd());
        $this->subject->unsetData('key_a_2nd');

        $this->assertSame('value_a_3rd', $this->subject->getData('key_a3rd'));
        $this->assertSame('value_a_3rd', $this->subject->getKeyA3rd());
        $this->subject->unsetData('key_a3rd');

        $this->assertSame(['left' => 'over'], $this->subject->getData());

        $this->subject->unsetData();
        $this->assertSame([], $this->subject->getData());
        $this->assertTrue($this->subject->isEmpty());

        try {
            /** @phpstan-ignore-next-line */
            $this->subject->notData();
            $this->fail('Invalid __call');
        } catch (Varien_Exception $exception) {
            $this->assertStringStartsWith('Invalid method', $exception->getMessage());
        }
    }

    /**
     * @group Varien_Object
     */
    public function testOffset(): void
    {
        $this->assertFalse($this->subject->offsetExists('off'));

        $this->subject->offsetSet('off', 'set');
        $this->assertTrue($this->subject->offsetExists('off'));
        $this->assertSame('set', $this->subject->offsetGet('off'));
        $this->assertSame(null, $this->subject->offsetGet('not-exists'));

        $this->subject->offsetUnset('off');
        $this->assertFalse($this->subject->offsetExists('off'));
    }
}
