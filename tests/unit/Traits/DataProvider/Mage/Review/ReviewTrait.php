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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Review;

use Generator;

trait ReviewTrait
{
    public function provideValidateReviewData(): Generator
    {
        $validReview = [
            'getTitle' => 'Great product',
            'getDetail' => 'I really liked this product.',
            'getNickname' => 'JohnDoe',
            'getCustomerId' => 1,
            'getEntityId' => 1,
            'getStoreId' => 1,
        ];

        yield 'valid data' => [
            true,
            $validReview,
        ];

        $data = $validReview;
        $data['getTitle'] = '';
        yield 'missing title' => [
            ['Review summary can\'t be empty'],
            $data,
        ];

        $data = $validReview;
        $data['getDetail'] = '';
        yield 'missing detail' => [
            ['Review can\'t be empty'],
            $data,
        ];

        $data = $validReview;
        $data['getNickname'] = '';
        yield 'missing nickname' => [
            ['Nickname can\'t be empty'],
            $data,
        ];
    }
}
