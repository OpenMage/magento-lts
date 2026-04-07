<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Model\Config\Source;

// use Mage;
// use Mage_Wishlist_Model_Config_Source_Summary as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Wishlist\Model\Config\Source\SummaryTrait;

final class SummaryTest extends OpenMageTest
{
    use SummaryTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('wishlist/config_source_summary');
        self::markTestSkipped('');
    }
}
