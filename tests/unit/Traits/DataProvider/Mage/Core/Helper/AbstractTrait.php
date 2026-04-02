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
}
