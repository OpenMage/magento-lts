<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Recurring\Profile;

# use Mage;
# use Mage_Sales_Model_Resource_Recurring_Profile_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Recurring\Profile\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('sales/resource_recurring_profile_collection');
        self::markTestSkipped('');
    }
}
