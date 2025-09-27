<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model;

use Generator;
use Varien_Object;

trait UrlTrait
{
    public function provideGeneratePathData(): Generator
    {
        $category = new Varien_Object([
            'id'        => '999',
            'store_id'  => '1',
            'url_key'   => '',
            'name'      => 'category',

        ]);
        $product = new Varien_Object([
            'id'        => '999',
            'name'      => 'product',
        ]);

        yield 'test exception' => [
            'Please specify either a category or a product, or both.',
            'request',
            null,
            null,
        ];
        yield 'request' => [
            'product.html',
            'request',
            $product,
            $category,
        ];
        //        yield 'request w/o product' => [
        //            '-.html',
        //            'request',
        //            null,
        //            $category,
        //        ];
        yield 'target category' => [
            'catalog/category/view/id/999',
            'target',
            null,
            $category,
        ];
        yield 'target product' => [
            'catalog/product/view/id/999',
            'target',
            $product,
            $category,
        ];
    }

    public function provideGetSluggerConfig(): Generator
    {
        yield 'de_DE' => [
            ['de_DE' => [
                '%' => 'prozent',
                '&' => 'und',
            ]],
            'de_DE',
        ];
        yield 'en_US' => [
            ['en_US' => [
                '%' => 'percent',
                '&' => 'and',
            ]],
            'en_US',
        ];
        yield 'fr_FR' => [
            ['fr_FR' => [
                '%' => 'pour cent',
                '&' => 'et',
            ]],
            'fr_FR',
        ];
    }
}
