<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Payment\Block;

use Mage;
use Mage_Payment_Block_Info as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Payment\Block\InfoTrait;

final class InfoTest extends OpenMageTest
{
    use InfoTrait;

    /**
     * Mage_Sales_Model_Order_Pdf_Abstract::insertOrder() splits the payment PDF output at
     * {{pdf_row_separator}} and draws each row as plain text, in whichever area the PDF is rendered
     *
     * @dataProvider provideToPdfArea
     * @group Block
     */
    public function testToPdfReturnsPlainTextRows(string $area): void
    {
        $subject = new Subject();
        $subject->setArea($area);
        $subject->setInfo(Mage::getModel('payment/info')->setMethod('free'));

        $pdf = (string) $subject->toPdf();

        self::assertStringNotContainsString('<', $pdf);
        $rows = array_values(array_filter(
            array_map(trim(...), explode('{{pdf_row_separator}}', $pdf)),
            static fn(string $row): bool => $row !== '',
        ));
        self::assertSame('No Payment Information Required', $rows[0] ?? null);
    }
}
