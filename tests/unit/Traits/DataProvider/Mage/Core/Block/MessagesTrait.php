<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Block;

use Generator;

trait MessagesTrait
{
    /**
     * @return Generator<string, array{string, string}, mixed, void>
     */
    public function provideMessageTextAndForbiddenSubstrings(): Generator
    {
        yield 'script tag' => [
            '<script>alert("xss")</script>',
            '<script',
        ];

        yield 'script tag with space' => [
            '<script >alert("xss")</script>',
            '<script',
        ];

        yield 'img onerror' => [
            '<img onerror=alert(1)>',
            '<img',
        ];

        yield 'svg onload' => [
            '<svg onload=alert(1)>',
            '<svg',
        ];

        yield 'iframe src' => [
            '<iframe src="javascript:alert(1)"></iframe>',
            '<iframe',
        ];

        yield 'div onclick' => [
            '<div onclick=alert(1)>click me</div>',
            '<div',
        ];

        yield 'a href javascript' => [
            '<a href="javascript:alert(1)">click</a>',
            'javascript:',
        ];

        yield 'body onload' => [
            '<body onload=alert(1)>',
            'onload',
        ];

        yield 'span onclick' => [
            '<span onclick=alert(1)>click me</span>',
            '<span onclick',
        ];
    }
}
