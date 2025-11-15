<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product;

use Mage;
use Mage_Catalog_Model_Product_Image as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\ImageTrait;

final class ImageTest extends OpenMageTest
{
    use ImageTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product_image');
    }

    /**
     * @dataProvider provideSetSizeData
     * @group Model
     */
    public function testSetSize(array $expected, string $value): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setSize($value));
        self::assertSame($expected['width'], self::$subject->getWidth(), 'Width does not match');
        self::assertSame($expected['height'], self::$subject->getHeight(), 'Height does not match');
    }
}
