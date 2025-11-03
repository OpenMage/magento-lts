<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
            ["Review summary can't be empty"],
            $data,
        ];

        $data = $validReview;
        $data['getDetail'] = '';
        yield 'missing detail' => [
            ["Review can't be empty"],
            $data,
        ];

        $data = $validReview;
        $data['getNickname'] = '';
        yield 'missing nickname' => [
            ["Nickname can't be empty"],
            $data,
        ];
    }
}
