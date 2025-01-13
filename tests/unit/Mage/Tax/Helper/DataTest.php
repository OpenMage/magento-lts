<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Helper;

use Generator;
use Mage;
use Mage_Tax_Helper_Data;
use Mage_Tax_Model_Calculation;
use Mage_Tax_Model_Config;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Mage_Tax_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('tax/data');
    }

    /**
     * @covers Mage_Tax_Helper_Data::getPostCodeSubStringLength()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetPostCodeSubStringLength(): void
    {
        static::assertSame(10, $this->subject->getPostCodeSubStringLength());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getConfig()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetConfig(): void
    {
        static::assertInstanceOf(Mage_Tax_Model_Config::class, $this->subject->getConfig());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getCalculator()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetCalculator(): void
    {
        static::assertInstanceOf(Mage_Tax_Model_Calculation::class, $this->subject->getCalculator());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @doesNotPerformAssertions
     */
    public function testGetProductPrice(): void
    {
        #$this->assertSame('', $this->subject->getProductPrice());
        static::markTestIncomplete();
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testPriceIncludesTax(): void
    {
        static::assertFalse($this->subject->priceIncludesTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxAfterDiscount()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testApplyTaxAfterDiscount(): void
    {
        static::assertTrue($this->subject->applyTaxAfterDiscount());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getIncExcText()
     * @dataProvider provideGetIncExcText
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetIncExcText(string $expectedResult, bool $flag): void
    {
        static::assertStringContainsString($expectedResult, $this->subject->getIncExcText($flag));
    }

    public function provideGetIncExcText(): Generator
    {
        yield 'true' => [
            'Incl. Tax',
            true,
        ];
        yield 'false' => [
            'Excl. Tax',
            false,
        ];
    }

    /**
     * @covers Mage_Tax_Helper_Data::getPriceDisplayType()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetPriceDisplayType(): void
    {
        static::assertSame(1, $this->subject->getPriceDisplayType());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @doesNotPerformAssertions
     */
    public function testNeedPriceConversion(): void
    {
        #$this->assertSame(1, $this->subject->needPriceConversion());
        static::markTestIncomplete();
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @doesNotPerformAssertions
     */
    public function testGetPriceFormat(): void
    {
        #$this->assertSame('', $this->subject->getPriceFormat());
        static::markTestIncomplete();
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @group UsesSampleDataFlag
     */
    public function testGetTaxRatesByProductClass(): void
    {
        $expectedResult = defined('USES_SAMPLEDATA') && USES_SAMPLEDATA === true ?
            '{"value_2":9,"value_4":0,"value_6":0}' : '{"value_2":8.25,"value_4":0}';
        static::assertSame($expectedResult, $this->subject->getTaxRatesByProductClass());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @group UsesSampleDataFlag
     */
    public function testGetAllRatesByProductClass(): void
    {
        $expectedResult = defined('USES_SAMPLEDATA') && USES_SAMPLEDATA === true ?
            '{"value_2":9,"value_4":0,"value_6":0}' : '{"value_2":8.25,"value_4":0}';
        static::assertSame($expectedResult, $this->subject->getAllRatesByProductClass());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     * @doesNotPerformAssertions
     */
    public function testGetPrice(): void
    {
        #$this->assertFalse($this->subject->getPrice());
        static::markTestIncomplete();
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayPriceIncludingTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayPriceIncludingTax(): void
    {
        static::assertFalse($this->subject->displayPriceIncludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayPriceExcludingTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayPriceExcludingTax(): void
    {
        static::assertTrue($this->subject->displayPriceExcludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayBothPrices()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayBothPrices(): void
    {
        static::assertFalse($this->subject->displayBothPrices());
    }

    /**
     * @dataProvider provideGetIncExcTaxLabel
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetIncExcTaxLabel($expectedResult, bool $flag): void
    {
        static::assertStringContainsString($expectedResult, $this->subject->getIncExcTaxLabel($flag));
    }

    public function provideGetIncExcTaxLabel(): Generator
    {
        yield 'true' => [
            '(Incl. Tax)',
            true,
        ];
        yield 'false' => [
            '(Excl. Tax)',
            false,
        ];
    }

    /**
     * @covers Mage_Tax_Helper_Data::shippingPriceIncludesTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testShippingPriceIncludesTax(): void
    {
        static::assertFalse($this->subject->shippingPriceIncludesTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getShippingPriceDisplayType()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetShippingPriceDisplayType(): void
    {
        static::assertSame(1, $this->subject->getShippingPriceDisplayType());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingPriceIncludingTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayShippingPriceIncludingTax(): void
    {
        static::assertFalse($this->subject->displayShippingPriceIncludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingPriceExcludingTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayShippingPriceExcludingTax(): void
    {
        static::assertTrue($this->subject->displayShippingPriceExcludingTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::displayShippingBothPrices()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDisplayShippingBothPrices(): void
    {
        static::assertFalse($this->subject->displayShippingBothPrices());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getShippingTaxClass()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetShippingTaxClass(): void
    {
        static::assertSame(0, $this->subject->getShippingTaxClass(null));
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetShippingPrice(): void
    {
        static::assertEqualsWithDelta(100.0, $this->subject->getShippingPrice(100.0), PHP_FLOAT_EPSILON);
    }

    /**
     * @covers Mage_Tax_Helper_Data::discountTax()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testDiscountTax(): void
    {
        static::assertFalse($this->subject->discountTax());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getTaxBasedOn()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetTaxBasedOn(): void
    {
        static::assertSame('shipping', $this->subject->getTaxBasedOn());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxOnCustomPrice()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testApplyTaxOnCustomPrice(): void
    {
        static::assertTrue($this->subject->applyTaxOnCustomPrice());
    }

    /**
     * @covers Mage_Tax_Helper_Data::applyTaxOnOriginalPrice()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testApplyTaxOnOriginalPrice(): void
    {
        static::assertFalse($this->subject->applyTaxOnOriginalPrice());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetCalculationSequence(): void
    {
        static::assertSame('1_0', $this->subject->getCalculationSequence());
    }

    /**
     * @covers Mage_Tax_Helper_Data::getCalculationAgorithm()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testGetCalculationAgorithm(): void
    {
        static::assertSame('TOTAL_BASE_CALCULATION', $this->subject->getCalculationAgorithm());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testIsWrongDisplaySettingsIgnored(): void
    {
        static::assertFalse($this->subject->isWrongDisplaySettingsIgnored());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testIsWrongDiscountSettingsIgnored(): void
    {
        static::assertFalse($this->subject->isWrongDiscountSettingsIgnored());
    }

    /**
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testIsConflictingFptTaxConfigurationSettingsIgnored(): void
    {
        static::assertFalse($this->subject->isConflictingFptTaxConfigurationSettingsIgnored());
    }

    /**
     * @covers Mage_Tax_Helper_Data::isCrossBorderTradeEnabled()
     * @group Mage_Tax
     * @group Mage_Tax_Helper
     */
    public function testIsCrossBorderTradeEnabled(): void
    {
        static::assertFalse($this->subject->isCrossBorderTradeEnabled());
    }
}
