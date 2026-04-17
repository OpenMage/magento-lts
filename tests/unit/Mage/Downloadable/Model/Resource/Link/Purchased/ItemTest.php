<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Resource\Link\Purchased;

// use Mage;
// use Mage_Downloadable_Model_Resource_Link_Purchased_Item as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Model\Resource\Link\Purchased\ItemTrait;

final class ItemTest extends OpenMageTest
{
    use ItemTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('downloadable/resource_link_purchased_item');
        self::markTestSkipped('');
    }
}
