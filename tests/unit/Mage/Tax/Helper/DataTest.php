<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Helper;

use Mage;
use Mage_Tax_Helper_Data as Subject;
use Mage_Tax_Model_Calculation;
use Mage_Tax_Model_Config;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\TaxTrait;

final class DataTest extends OpenMageTest
{
    use TaxTrait;

    private static Subject $subject;

    public const SKIP_INCOMPLETE = 'incomplete';

    public const SKIP_WITH_LOCAL_DATA = 'Constant DATA_MAY_CHANGED is defined.';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('tax/data');
    }

    /**
     * @covers Mage_Tax_Helper_Data::getPostCodeSubStringLength()
     * @group Helper
     */
    public function testGetPostCodeSubStringLength(): void
    {
        self::assertSame(10, self::$subject->getPostCodeSubStringLength());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getConfig()
     * @group Helper
     */
    public function testGetConfig(): void
    {
        self::assertInstanceOf(Mage_Tax_Model_Config::class, self::$subject->getConfig());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getCalculator()
     * @group Helper
     */
    public function testGetCalculator(): void
    {
        self::assertInstanceOf(Mage_Tax_Model_Calculation::class, self::$subject->getCalculator());
    }

    /**
     * @group Helper
     * @doesNotPerformAssertions
     */
    public function testGetProductPrice(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertSame('', self::$subject->getProductPrice());
    }

    /**
     * @group Helper
     */
    public function testPriceIncludesTax(): void
    {
        self::assertFalse(self::$subject->priceIncludesTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxAfterDiscount()
     * @group Helper
     */
    public function testApplyTaxAfterDiscount(): void
    {
        self::assertTrue(self::$subject->applyTaxAfterDiscount());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getIncExcText()
     * @dataProvider provideGetIncExcText
     * @group Helper
     */
    public function testGetIncExcText(string $expectedResult, bool $flag): void
    {
        self::assertStringContainsString($expectedResult, self::$subject->getIncExcText($flag));
    }

    /**
     * @covers Mage_Tax_Helper_Data::getPriceDisplayType()
     * @group Helper
     */
    public function testGetPriceDisplayType(): void
    {
        self::assertSame(1, self::$subject->getPriceDisplayType());
    }

    /**
     * @group Helper
     * @doesNotPerformAssertions
     */
    public function testNeedPriceConversion(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertSame(1, self::$subject->needPriceConversion());
    }

    /**
     * @group Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetPriceFormat(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertSame('', self::$subject->getPriceFormat());
    }

    /**
     * @group Helper
     */
    public function testGetTaxRatesByProductClass(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            self::markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }

        self::assertSame('{"value_2":8.25,"value_4":0}', self::$subject->getTaxRatesByProductClass());
    }

    /**
     * @group Helper
     */
    public function testGetAllRatesByProductClass(): void
    {
        if (defined('DATA_MAY_CHANGED')) {
            self::markTestSkipped(self::SKIP_WITH_LOCAL_DATA);
        }

        self::assertSame('{"value_2":8.25,"value_4":0}', self::$subject->getAllRatesByProductClass());
    }

    /**
     * @group Helper
     * @doesNotPerformAssertions
     */
    public function testGetPrice(): void
    {
        self::markTestSkipped(self::SKIP_INCOMPLETE);
        /** @phpstan-ignore deadCode.unreachable */
        self::assertFalse(self::$subject->getPrice());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayPriceIncludingTax()
     * @group Helper
     */
    public function testDisplayPriceIncludingTax(): void
    {
        self::assertFalse(self::$subject->displayPriceIncludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayPriceExcludingTax()
     * @group Helper
     */
    public function testDisplayPriceExcludingTax(): void
    {
        self::assertTrue(self::$subject->displayPriceExcludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayBothPrices()
     * @group Helper
     */
    public function testDisplayBothPrices(): void
    {
        self::assertFalse(self::$subject->displayBothPrices());
    }

    /**
     * @dataProvider provideGetIncExcTaxLabel
     * @group Helper
     */
    public function testGetIncExcTaxLabel(string $expectedResult, bool $flag): void
    {
        self::assertStringContainsString($expectedResult, self::$subject->getIncExcTaxLabel($flag));
    }

    /**
     * @covers Mage_Tax_Helper_Data::shippingPriceIncludesTax()
     * @group Helper
     */
    public function testShippingPriceIncludesTax(): void
    {
        self::assertFalse(self::$subject->shippingPriceIncludesTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getShippingPriceDisplayType()
     * @group Helper
     */
    public function testGetShippingPriceDisplayType(): void
    {
        self::assertSame(1, self::$subject->getShippingPriceDisplayType());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingPriceIncludingTax()
     * @group Helper
     */
    public function testDisplayShippingPriceIncludingTax(): void
    {
        self::assertFalse(self::$subject->displayShippingPriceIncludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingPriceExcludingTax()
     * @group Helper
     */
    public function testDisplayShippingPriceExcludingTax(): void
    {
        self::assertTrue(self::$subject->displayShippingPriceExcludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingBothPrices()
     * @group Helper
     */
    public function testDisplayShippingBothPrices(): void
    {
        self::assertFalse(self::$subject->displayShippingBothPrices());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getShippingTaxClass()
     * @group Helper
     */
    public function testGetShippingTaxClass(): void
    {
        self::assertSame(0, self::$subject->getShippingTaxClass(null));
    }

    /**
     * @group Helper
     */
    public function testGetShippingPrice(): void
    {
        self::assertEqualsWithDelta(100.0, self::$subject->getShippingPrice(100.0), PHP_FLOAT_EPSILON);
    }

    /**
     * @covers Mage_Tax_Helper_Data::discountTax()
     * @group Helper
     */
    public function testDiscountTax(): void
    {
        self::assertFalse(self::$subject->discountTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getTaxBasedOn()
     * @group Helper
     */
    public function testGetTaxBasedOn(): void
    {
        self::assertSame('shipping', self::$subject->getTaxBasedOn());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxOnCustomPrice()
     * @group Helper
     */
    public function testApplyTaxOnCustomPrice(): void
    {
        self::assertTrue(self::$subject->applyTaxOnCustomPrice());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxOnOriginalPrice()
     * @group Helper
     */
    public function testApplyTaxOnOriginalPrice(): void
    {
        self::assertFalse(self::$subject->applyTaxOnOriginalPrice());
    }

    /**
     * @group Helper
     */
    public function testGetCalculationSequence(): void
    {
        self::assertSame('1_0', self::$subject->getCalculationSequence());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getCalculationAgorithm()
     * @group Helper
     */
    public function testGetCalculationAgorithm(): void
    {
        self::assertSame('TOTAL_BASE_CALCULATION', self::$subject->getCalculationAgorithm());
    }

    /**
     * @group Helper
     */
    public function testIsWrongDisplaySettingsIgnored(): void
    {
        self::assertFalse(self::$subject->isWrongDisplaySettingsIgnored());
    }

    /**
     * @group Helper
     */
    public function testIsWrongDiscountSettingsIgnored(): void
    {
        self::assertFalse(self::$subject->isWrongDiscountSettingsIgnored());
    }

    /**
     * @group Helper
     */
    public function testIsConflictingFptTaxConfigurationSettingsIgnored(): void
    {
        self::assertFalse(self::$subject->isConflictingFptTaxConfigurationSettingsIgnored());
    }

    /**
     * @covers Mage_Tax_Helper_Data::isCrossBorderTradeEnabled()
     * @group Helper
     */
    public function testIsCrossBorderTradeEnabled(): void
    {
        self::assertFalse(self::$subject->isCrossBorderTradeEnabled());
    }
}
