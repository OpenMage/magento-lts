<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Model\Product\Attribute\Source\Price;

// use Mage;
// use Mage_Bundle_Model_Product_Attribute_Source_Price_View as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Model\Product\Attribute\Source\Price\ViewTrait;

final class ViewTest extends OpenMageTest
{
    use ViewTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('bundle/product_attribute_source_price_view');
        self::markTestSkipped('');
    }
}
