<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
