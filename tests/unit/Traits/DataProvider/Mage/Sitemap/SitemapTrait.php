<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sitemap;

use Generator;

trait SitemapTrait
{
    public function provideGetPreparedFilenameData(): Generator
    {
        yield 'default' => [
            [
                'getSitemapFilename' => 'text.xml',
            ],
        ];
    }

    public function provideGenerateXmlData(): Generator
    {
        yield 'default' => [
            [
                'isDeleted' => true,  # do not save to DB
                'getSitemapFilename' => '???phpunit.sitemap.xml',
            ],
        ];
    }
}
