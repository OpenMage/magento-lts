<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Rule\Condition\Product\Attribute;

use Mage;
use Mage_SalesRule_Model_Rule_Condition_Product_Attribute_Assigned as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Rule\Condition\Product\Attribute\AssignedTrait;

final class AssignedTest extends OpenMageTest
{
    use AssignedTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('salesrule/rule_condition_product_attribute_assigned');
    }
}
