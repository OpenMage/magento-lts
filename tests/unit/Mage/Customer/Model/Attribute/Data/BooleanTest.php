<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Attribute\Data;

// use Mage;
// use Mage_Customer_Model_Attribute_Data_Boolean as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Attribute\Data\BooleanTrait;

final class BooleanTest extends OpenMageTest
{
    use BooleanTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('customer/attribute_data_boolean');
        self::markTestSkipped('');
    }
}
