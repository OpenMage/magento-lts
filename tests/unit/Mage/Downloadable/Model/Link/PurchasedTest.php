<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Link;

// use Mage;
// use Mage_Downloadable_Model_Link_Purchased as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Model\Link\PurchasedTrait;

final class PurchasedTest extends OpenMageTest
{
    use PurchasedTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('downloadable/link_purchased');
        self::markTestSkipped('');
    }
}
