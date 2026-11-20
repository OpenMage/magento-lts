<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Sales\Total\Quote;

use Generator;
use Mage;
use Mage_Sales_Model_Quote_Address;
use Mage_Sales_Model_Quote_Item;
use Mage_Tax_Model_Calculation;
use Mage_Tax_Model_Sales_Total_Quote_Tax as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\Sales\Total\Quote\TaxTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use ReflectionProperty;
use Varien_Object;

final class TaxTest extends OpenMageTest
{
    use TaxTrait;

    private const APPLIED_RATES = [
        ['id' => 'US-CA-*-Rate 1', 'percent' => 8.25, 'rates' => []],
    ];

    /**
     * The applied rates are grouped by the quote item id, which is null for an unsaved quote item
     *
     * @param list<string> $mockedMethods
     *
     * @group Model
     */
    #[DataProvider('provideProcessItemTaxGroupsUnsavedItem')]
    public function testProcessItemTaxGroupsUnsavedItem(string $method, array $mockedMethods): void
    {
        $subject = $this->createPartialMock(Subject::class, $mockedMethods);
        $subject->setCode('tax');

        $calculator = $this->createPartialMock(Mage_Tax_Model_Calculation::class, ['getRate', 'getAppliedRates']);
        $calculator->method('getRate')->willReturn(8.25);
        $calculator->method('getAppliedRates')->willReturn(self::APPLIED_RATES);
        (new ReflectionProperty(Subject::class, '_calculator'))->setValue($subject, $calculator);
        (new ReflectionProperty(Subject::class, '_store'))->setValue($subject, Mage::app()->getStore());

        /** @var Mage_Sales_Model_Quote_Address $address */
        $address = Mage::getModel('sales/quote_address');
        (new ReflectionMethod(Subject::class, '_setAddress'))->invoke($subject, $address);

        /** @var Mage_Sales_Model_Quote_Item $item */
        $item = Mage::getModel('sales/quote_item');
        $item->setData('product', new Varien_Object(['tax_class_id' => 2]));

        $taxRateRequest = new Varien_Object();
        $taxGroups = [];
        /** @var array<int|string, mixed> $itemTaxGroups */
        $itemTaxGroups = [];
        $args = $method === '_totalBaseProcessItemTax'
            ? [$item, $taxRateRequest, &$taxGroups, &$itemTaxGroups, true]
            : [$address, $item, $taxRateRequest, &$itemTaxGroups, true];

        /** @var list<string> $deprecations */
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, E_DEPRECATED);

        try {
            (new ReflectionMethod(Subject::class, $method))->invokeArgs($subject, $args);
        } finally {
            restore_error_handler();
        }

        self::assertSame(['' => self::APPLIED_RATES], $itemTaxGroups);
        self::assertSame([], $deprecations);
    }

    public static function provideProcessItemTaxGroupsUnsavedItem(): Generator
    {
        yield 'unit base' => ['_unitBaseProcessItemTax', ['_calcUnitTaxAmount', '_saveAppliedTaxes']];
        yield 'row base' => ['_rowBaseProcessItemTax', ['_calcRowTaxAmount', '_saveAppliedTaxes']];
        yield 'total base' => ['_totalBaseProcessItemTax', ['_aggregateTaxPerRate']];
    }
}
