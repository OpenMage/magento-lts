<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog;

use Generator;

trait CatalogTrait
{
    public static string $testSting = '--a & B, x% @ ä ö ü ™--';

    public function provideFormatUrlKey(): Generator
    {
        yield 'de_DE' => [
            'a-und-b-x-prozent-at-ae-oe-ue-tm',
            'de_DE',
        ];
        yield 'en_US' => [
            'a-and-b-x-percent-at-a-o-u-tm',
            'en_US',
        ];
        yield 'es_ES' => [
            'a-et-b-x-por-ciento-at-a-o-u-tm',
            'es_ES',
        ];
        yield 'fr_FR' => [
            'a-et-b-x-pour-cent-at-a-o-u-tm',
            'fr_FR',
        ];
        yield 'it_IT' => [
            'a-e-b-x-per-cento-at-a-o-u-tm',
            'it_IT',
        ];
    }

    public function getTestString(): string
    {
        return static::$testSting;
    }
}
