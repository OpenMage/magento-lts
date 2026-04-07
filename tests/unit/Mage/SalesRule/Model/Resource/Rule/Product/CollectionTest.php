<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Rule\Product;

// use Mage;
// use Mage_SalesRule_Model_Resource_Rule_Product_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\Rule\Product\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('salesrule/resource_rule_product_collection');
        self::markTestSkipped('');
    }
}
