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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms;

use Generator;
use Mage_Cms_Helper_Page;

trait CmsTrait
{
    public static string $testString = '0123456789';

    public function provideGetUsedInStoreConfigPaths(): Generator
    {
        $default = [
            0 => Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE,
            1 => Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE,
            2 => Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE,
        ];

        yield 'null' => [
            [],
            null,
        ];
        yield 'empty array' => [
            $default,
            [],
        ];

        $custom = [
            'my/first/path',
            'my/second/path',
        ];

        yield 'custom paths' => [
            array_merge($default, $custom),
            $custom,
        ];
    }

    public function provideGetShortFilename(): Generator
    {
        yield 'full length' => [
            '0123456789',
            $this->getTestString(),
            20,
        ];
        yield 'truncated' => [
            '01234...',
            $this->getTestString(),
            5,
        ];
    }

    public function getTestString(): string
    {
        return static::$testString;
    }
}
