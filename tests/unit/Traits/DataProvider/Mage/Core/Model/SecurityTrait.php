<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
