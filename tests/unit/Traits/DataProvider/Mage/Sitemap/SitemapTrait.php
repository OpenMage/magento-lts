<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap;

use Generator;

trait SitemapTrait
{
    public static function provideGetPreparedFilenameData(): Generator
    {
        yield 'default' => [
            [
                'sitemap_filename' => 'text.xml',
            ],
        ];
    }

    /**
     * @return Generator<string, array{string, string}>
     */
    public static function provideGetHomePageIdentifierData(): Generator
    {
        yield 'identifier' => ['home', 'home'];
        yield 'identifier with page id' => ['home', 'home|2'];
    }

    public static function provideGenerateXmlData(): Generator
    {
        yield 'default' => [
            [
                'sitemap_filename' => '???phpunit.sitemap.xml',
            ],
        ];
    }
}
