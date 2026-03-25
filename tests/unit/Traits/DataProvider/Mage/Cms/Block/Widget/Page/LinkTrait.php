<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Block\Widget\Page;

use Generator;
use Mage_Cms_Helper_Page;

trait LinkTrait
{
    public static array $defaults = [
        'custom_title' => 'Custom Title',
        'custom_text'  => 'Custom Text',
    ];

    public static array $tests = [
        'empty' => 'empty',
        'href' => 'href is set',
        'no_data' => 'no data is set',
        'page_id' => 'page_id is set',
        'text' => 'text is set',
        'title' => 'title is set',
    ];

    public function provideGetHrefData(): Generator
    {
        $emptyData = [
            'href'    => null,
            'page_id' => null,
        ];

        #$data = $emptyData;
        #$data['page_id'] = 1;
        #yield 'page_id is set' => [
        #    'https://magento-lts.ddev.site/no-route',
        #    $data,
        #];

        $data = $emptyData;
        $data['href'] = 'home';
        yield self::$tests['href'] => [
            'home',
            $data,
        ];

        yield self::$tests['empty'] => [
            '',
            $emptyData,
        ];

        yield self::$tests['no_data'] => [
            '',
            [],
        ];
    }

    public function provideGetTitleData(): Generator
    {
        $emptyData = [
            'href'    => null,
            'page_id' => null,
            'title'   => null,
        ];

        $data = $emptyData;
        $data['title'] = self::$defaults['custom_title'];
        yield self::$tests['title'] => [
            self::$defaults['custom_title'],
            $data,
        ];

        $data = $emptyData;
        $data['page_id'] = 1;
        yield self::$tests['page_id'] => [
            '404 Not Found 1',
            $data,
        ];

        $data = $emptyData;
        $data['href'] = 'home';
        yield self::$tests['href'] => [
            'Home page',
            $data,
        ];

        yield self::$tests['empty'] => [
            '',
            $emptyData,
        ];

        yield self::$tests['no_data'] => [
            '',
            [],
        ];
    }

    public function provideGetAnchorTextData(): Generator
    {
        $emptyData = [
            'href'    => null,
            'page_id' => null,
            'anchor_text'   => null,
            'test'   => 'Test',
        ];

        $data = $emptyData;
        $data['anchor_text'] = self::$defaults['custom_text'];
        yield self::$tests['text'] => [
            self::$defaults['custom_text'],
            $data,
        ];

        $data = $emptyData;
        $data['title'] = self::$defaults['custom_title'];
        yield self::$tests['title'] => [
            self::$defaults['custom_title'],
            $data,
        ];

        $data = $emptyData;
        $data['page_id'] = 1;
        yield self::$tests['page_id'] => [
            '404 Not Found 1',
            $data,
        ];

        $data = $emptyData;
        $data['href'] = 'home';
        yield self::$tests['href'] => [
            'Home page',
            $data,
        ];

        yield self::$tests['empty'] => [
            null,
            $emptyData,
        ];

        yield self::$tests['no_data'] => [
            null,
            [],
        ];
    }
}
