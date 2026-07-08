<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Review;

use Generator;

trait ReviewTrait
{
    public static function provideValidateReviewData(): Generator
    {
        $validReview = [
            'title' => 'Great product',
            'detail' => 'I really liked this product.',
            'nickname' => 'JohnDoe',
            'customer_id' => 1,
            'entity_id' => 1,
            'store_id' => 1,
        ];

        yield 'valid data' => [
            true,
            $validReview,
        ];

        $data = $validReview;
        $data['title'] = '';
        yield 'missing title' => [
            ["Review summary can't be empty"],
            $data,
        ];

        $data = $validReview;
        $data['detail'] = '';
        yield 'missing detail' => [
            ["Review can't be empty"],
            $data,
        ];

        $data = $validReview;
        $data['nickname'] = '';
        yield 'missing nickname' => [
            ["Nickname can't be empty"],
            $data,
        ];
    }
}
