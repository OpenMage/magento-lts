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
        $str = '<a href="https://openmage.org/">OpenMage <b>is <i>super</i>-cool</b></a>';

        yield 'null allowed tags, no escape' => [
            'OpenMage is super-cool',
            $str,
            null,
            false,
        ];
        yield 'empty array allowed tags, no escape' => [
            'OpenMage is super-cool',
            $str,
            [],
            false,
        ];
        yield 'null array allowed tags, no escape' => [
            'OpenMage is super-cool',
            $str,
            [null],
            false,
        ];
        yield 'empty string allowed tags, no escape' => [
            'OpenMage is super-cool',
            $str,
            '',
            false,
        ];
        yield 'null allowed tags, escape' => [
            'OpenMage is super-cool',
            $str,
            null,
            true,
        ];
        yield 'empty array allowed tags, escape' => [
            'OpenMage is super-cool',
            $str,
            [],
            true,
        ];
        yield 'null array allowed tags, escape' => [
            'OpenMage is super-cool',
            $str,
            [null],
            true,
        ];
        yield 'empty string allowed tags, escape' => [
            'OpenMage is super-cool',
            $str,
            '',
            true,
        ];
        yield 'b allowed tags, no escape' => [
            'OpenMage <b>is super-cool</b>',
            $str,
            '<b>',
            false,
        ];
        yield 'b allowed tags, escape' => [
            'OpenMage &lt;b&gt;is super-cool&lt;/b&gt;',
            $str,
            '<b>',
            true,
        ];
        yield 'b simple allowed tags, no escape' => [
            'OpenMage is super-cool',
            $str,
            'b',
            false,
        ];
        yield 'b simple allowed tags, escape' => [
            'OpenMage is super-cool',
            $str,
            'b',
            true,
        ];
        yield 'b array allowed tags, no escape' => [
            'OpenMage <b>is super-cool</b>',
            $str,
            ['b'],
            false,
        ];
        yield 'b array allowed tags, escape' => [
            'OpenMage <b>is super-cool</b>',
            $str,
            ['b'],
            true,
        ];
    }
}
