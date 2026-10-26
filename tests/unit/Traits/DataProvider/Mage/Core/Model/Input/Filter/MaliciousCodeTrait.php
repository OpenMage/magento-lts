<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Input\Filter;

use Generator;

trait MaliciousCodeTrait
{
    /**
     * @return Generator<string, array{string}>
     */
    public static function provideEventHandlerPayloads(): Generator
    {
        yield 'well formed tag' => ['<img src=x onerror=alert(1)>'];
        yield 'unclosed tag' => ['<img src=x onerror=alert(1)'];
        yield 'unclosed tag inside markup' => ['<p>hi <img src=x onerror=alert(1)</p>'];
        yield 'slash separator' => ['<img/onerror=alert(1)>'];
        yield 'slash separator unclosed' => ['<img/onerror=alert(1)'];
        yield 'slash after attribute' => ['<img src=x/onerror=alert(1)>'];
        yield 'double slash' => ['<img src=x//onerror=alert(1)>'];
        yield 'handler not in the expression list' => ['<a href="#" onauxclick=alert(1)>x</a>'];
        yield 'svg handler' => ['<svg onpointerenter=alert(1)>x</svg>'];
        yield 'details ontoggle' => ['<details open ontoggle=alert(1)>'];
        yield 'javascript url' => ['<a href="javascript:alert(1)">x</a>'];
        yield 'script tag' => ['<script>alert(1)</script>'];
    }
}
