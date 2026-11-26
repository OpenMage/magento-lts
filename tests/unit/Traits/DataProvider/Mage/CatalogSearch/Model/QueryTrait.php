<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogSearch\Model;

use Generator;

/**
 * @phpstan-type ValidateData array{
 *     "query_text": ?string,
 *     "store_id": string
 * }
 */
trait QueryTrait
{
    /**
     * @return Generator<string, list{bool|string[], ValidateData}, void, void>
     */
    public static function provideValidateCatalogSearchQueryData(): Generator
    {
        yield 'test passes' => [
            true,
            [
                'query_text' => 'test',
                'store_id' => '1',
            ],
        ];
        yield 'test error empty' => [
            ['Search Query is required field.'],
            [
                'query_text' => '',
                'store_id' => '1',
            ],
        ];
        yield 'test error null' => [
            ['Search Query is required field.'],
            [
                'query_text' => null,
                'store_id' => '1',
            ],
        ];
    }
}
