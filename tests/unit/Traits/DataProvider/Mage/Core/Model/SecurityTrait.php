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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;
use Varien_Object;

trait SecurityTrait
{
    public static string $testString = 'This is a bold <b>string</b>';

    public function provideHtmlEscapedStringAsStringData(): Generator
    {
        yield 'tags null' => [
            'This is a bold &lt;b&gt;string&lt;/b&gt;',
            self::$testString,
            null,
        ];
        yield 'tags array' => [
            self::$testString,
            self::$testString,
            ['b'],
        ];
    }

    public function provideHtmlEscapedStringGetUnescapedValueData(): Generator
    {
        yield 'tags null' => [
            self::$testString,
            self::$testString,
            null,
        ];
        yield 'tags array' => [
            self::$testString,
            self::$testString,
            ['some-invalid-value'],
        ];
    }
}
