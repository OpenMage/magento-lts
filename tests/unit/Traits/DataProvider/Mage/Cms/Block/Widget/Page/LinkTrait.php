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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\Block\Widget\Page;

use Generator;
use Mage_Cms_Helper_Page;

trait LinkTrait
{
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
        yield 'href is set' => [
            'home',
            $data,
        ];

        yield 'empty' => [
            '',
            $emptyData,
        ];

        yield 'no data set' => [
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
        $data['title'] = 'Custom Title';
        yield 'title is set' => [
            'Custom Title',
            $data,
        ];

        $data = $emptyData;
        $data['page_id'] = 1;
        yield 'page_id is set' => [
            '404 Not Found',
            $data,
        ];

        $data = $emptyData;
        $data['href'] = 'home';
        yield 'href is set' => [
            'Madison Island',
            $data,
        ];

        yield 'empty' => [
            '',
            $emptyData,
        ];

        yield 'no data set' => [
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
        $data['anchor_text'] = 'Custom Text';
        yield 'text is set' => [
            'Custom Text',
            $data,
        ];

        $data = $emptyData;
        $data['title'] = 'Custom Title';
        yield 'title is set' => [
            'Custom Title',
            $data,
        ];

        $data = $emptyData;
        $data['page_id'] = 1;
        yield 'page_id is set' => [
            '404 Not Found',
            $data,
        ];

        $data = $emptyData;
        $data['href'] = 'home';
        yield 'href is set' => [
            'Madison Island',
            $data,
        ];

        yield 'empty' => [
            null,
            $emptyData,
        ];

        yield 'no data set' => [
            null,
            [],
        ];
    }
}
