<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use Override;
use Mage;
use Mage_Adminhtml_Helper_Addresses as Subject;
use Mage_Customer_Model_Attribute;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper\AddressTrait;

final class AddressesTest extends OpenMageTest
{
    use AddressTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/addresses');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Addresses::processStreetAttribute()
     * @group Helper
     */
    #[DataProvider('provideProcessStreetAttribute')]
    public function testProcessStreetAttribute(int $expectedResult, int $lines): void
    {
        $attribute = new Mage_Customer_Model_Attribute();
        $attribute->setScopeMultilineCount($lines);

        $result = self::$subject->processStreetAttribute($attribute);
        self::assertSame($expectedResult, $result->getScopeMultilineCount());
    }
}
