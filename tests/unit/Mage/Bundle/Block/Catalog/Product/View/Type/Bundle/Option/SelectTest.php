<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Block\Catalog\Product\View\Type\Bundle\Option;

use Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Select as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\SelectTrait;

final class SelectTest extends OpenMageTest
{
    use SelectTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
