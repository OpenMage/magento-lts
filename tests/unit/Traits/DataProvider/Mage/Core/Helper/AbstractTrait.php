<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;
use stdClass;

trait AbstractTrait
{
    public function provideEscapeHtmlData(): Generator
    {
        yield 'empty array' => [
            [],
            [],
            null,
        ];
        yield 'empty string' => [
            '',
            '',
            null,
        ];
        yield 'null' => [
            null,
            null,
            null,
        ];
        yield 'bool' => [
            true,
            true,
            null,
        ];
        yield 'int' => [
            0,
            0,
            null,
        ];
        $object = new stdClass();
        yield 'obj' => [
            $object,
            $object,
            null,
        ];
    }

    public function provideStripTagsData(): Generator
    {
        $plain = $plain;
        $html = '<a href="https://openmage.org/">OpenMage <b>is <i>super</i>-cool</b></a>';

        yield 'null allowed tags, no escape' => [
            $plain,
            $html,
            null,
            false,
        ];
        yield 'empty array allowed tags, no escape' => [
            $plain,
            $html,
            [],
            false,
        ];
        yield 'null, no escape' => [
            '',
            null,
            null,
            false,
        ];
        yield 'null, escape' => [
            '',
            null,
            [],
            true,
        ];
        yield 'null array allowed tags, no escape' => [
            $plain,
            $html,
            [null],
            false,
        ];
        yield 'empty string allowed tags, no escape' => [
            $plain,
            $html,
            '',
            false,
        ];
        yield 'null allowed tags, escape' => [
            $plain,
            $html,
            null,
            true,
        ];
        yield 'empty array allowed tags, escape' => [
            $plain,
            $html,
            [],
            true,
        ];
        yield 'null array allowed tags, escape' => [
            $plain,
            $html,
            [null],
            true,
        ];
        yield 'empty string allowed tags, escape' => [
            $plain,
            $html,
            '',
            true,
        ];
        yield 'a allowed tags, no escape' => [
            '<a href="https://openmage.org/">OpenMage is super-cool</a>',
            $html,
            '<a>',
            false,
        ];
        yield 'a allowed tags, escape' => [
            '&lt;a href=&quot;https://openmage.org/&quot;&gt;OpenMage is super-cool&lt;/a&gt;',
            $html,
            '<a>',
            true,
        ];
        yield 'b simple allowed tags, no escape' => [
            $plain,
            $html,
            'b',
            false,
        ];
        yield 'b simple allowed tags, escape' => [
            $plain,
            $html,
            'b',
            true,
        ];
        yield 'b array allowed tags, no escape' => [
            'OpenMage <b>is super-cool</b>',
            $html,
            ['b'],
            false,
        ];
        yield 'b array allowed tags, escape' => [
            'OpenMage <b>is super-cool</b>',
            $html,
            ['b'],
            true,
        ];
    }
}
