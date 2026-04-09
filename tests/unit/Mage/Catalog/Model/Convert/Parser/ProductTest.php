<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Convert\Parser;

// use Mage;
// use Mage_Catalog_Model_Convert_Parser_Product as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Convert\Parser\ProductTrait;

final class ProductTest extends OpenMageTest
{
    use ProductTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/convert_parser_product');
        self::markTestSkipped('');
    }
}
