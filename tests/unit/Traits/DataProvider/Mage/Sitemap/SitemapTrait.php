<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
