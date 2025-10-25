<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms;

use Generator;
use Mage_Adminhtml_Block_System_Config_Form;
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

    public function provideGetConfigLabelFromConfigPath(): Generator
    {
        yield 'home page' => [
            'Home Page',
            Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE,
        ];

        yield 'no cookie page' => [
            'No Cookies Page',
            Mage_Cms_Helper_Page::XML_PATH_NO_COOKIES_PAGE,
        ];

        yield 'no route page' => [
            'No Route Page',
            Mage_Cms_Helper_Page::XML_PATH_NO_ROUTE_PAGE,
        ];
    }

    public function provideGetScopeInfoFromConfigScope(): Generator
    {
        yield 'default' => [
            'Default Config',
            Mage_Adminhtml_Block_System_Config_Form::SCOPE_DEFAULT,
            '1',
        ];

        yield 'websites' => [
            'Main Website',
            Mage_Adminhtml_Block_System_Config_Form::SCOPE_WEBSITES,
            '1',
        ];

        yield 'stores' => [
            'Main Website',
            Mage_Adminhtml_Block_System_Config_Form::SCOPE_STORES,
            '1',
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
