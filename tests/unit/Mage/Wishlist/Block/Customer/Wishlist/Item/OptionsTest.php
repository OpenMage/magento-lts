<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Wishlist\Block\Customer\Wishlist\Item;

use Mage_Wishlist_Block_Customer_Wishlist_Item_Options as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Wishlist\Block\Customer\Wishlist\Item\OptionsTrait;

final class OptionsTest extends OpenMageTest
{
    use OptionsTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
