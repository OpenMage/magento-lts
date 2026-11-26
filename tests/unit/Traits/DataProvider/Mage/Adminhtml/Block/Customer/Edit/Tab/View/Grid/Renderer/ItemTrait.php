<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Customer\Edit\Tab\View\Grid\Renderer;

use Generator;
use Mage_Catalog_Model_Product_Type_Configurable;
use Mage_Downloadable_Model_Product_Type;

trait ItemTrait
{
    /**
     * @return Generator<string, array{list<array<string, mixed>>, string, list<array<string, string>>|list<string>}>
     */
    public static function provideGetOptionListData(): Generator
    {
        yield 'configurable attribute option labels are escaped' => [
            [
                ['label' => 'Color', 'value' => '&lt;img src=x onerror=alert(1)&gt;'],
                ['label' => 'Size', 'value' => 'Fish &amp; Chips'],
            ],
            Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE,
            [
                ['label' => 'Color', 'value' => '<img src=x onerror=alert(1)>'],
                ['label' => 'Size', 'value' => 'Fish & Chips'],
            ],
        ];

        yield 'downloadable link titles are escaped' => [
            [
                ['label' => 'Links', 'value' => ['&lt;img src=x onerror=alert(1)&gt;', 'Fish &amp; Chips']],
            ],
            Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE,
            ['<img src=x onerror=alert(1)>', 'Fish & Chips'],
        ];
    }
}
