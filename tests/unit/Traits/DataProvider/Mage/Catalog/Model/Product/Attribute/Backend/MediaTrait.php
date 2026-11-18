<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Attribute\Backend;

use Generator;

trait MediaTrait
{
    /**
     * @return Generator<string, array{string}>
     */
    public static function provideTraversalFileData(): Generator
    {
        yield 'parent segments' => ['/../../../../protected.txt'];
        yield 'parent segments with tmp suffix' => ['/../../../../protected.txt.tmp'];
        yield 'parent segment in the middle' => ['/a/../../../b/image.jpg'];
        yield 'trailing parent segment' => ['/a/b/..'];
        yield 'backslash parent segments' => ['\\..\\..\\..\\..\\protected.txt'];
        yield 'NUL byte' => ["/a/b/image.jpg\0.tmp"];
        yield 'empty string' => [''];
    }

    /**
     * @return Generator<string, array{string}>
     */
    public static function provideValidFileData(): Generator
    {
        yield 'dispersed tmp upload' => ['/a/b/image.jpg.tmp'];
        yield 'dispersed upload' => ['/a/b/image.jpg'];
        yield 'double dot inside a name' => ['/a/b/image..jpg'];
    }
}
