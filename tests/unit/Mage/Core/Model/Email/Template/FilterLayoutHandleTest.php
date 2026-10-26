<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Email\Template;

use Generator;
use Mage;
use Mage_Core_Model_Email_Template_Filter as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

/**
 * GHSA-fqq6-76qg-2r96 / GHSA-57g5-322c-h5p9: {{layout}} may only render whitelisted handles
 */
final class FilterLayoutHandleTest extends OpenMageTest
{
    /**
     * @dataProvider provideIsLayoutHandleAllowed
     * @group Model
     */
    public function testIsLayoutHandleAllowed(bool $expected, string $handle): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('core/email_template_filter');
        self::assertSame($expected, $subject->isLayoutHandleAllowed($handle));
    }

    public static function provideIsLayoutHandleAllowed(): Generator
    {
        yield 'core order items handle' => [true, 'sales_email_order_items'];
        yield 'core invoice items handle' => [true, 'sales_email_order_invoice_items'];
        yield 'core shipment items handle' => [true, 'sales_email_order_shipment_items'];
        yield 'core creditmemo items handle' => [true, 'sales_email_order_creditmemo_items'];
        yield 'empty handle' => [false, ''];
        yield 'default handle' => [false, 'default'];
        yield 'adminhtml handle' => [false, 'adminhtml_dashboard_index'];
        yield 'case variant' => [false, 'SALES_EMAIL_ORDER_ITEMS'];
    }

    /**
     * @group Model
     */
    public function testLayoutDirectiveRendersNothingForDisallowedHandle(): void
    {
        /** @var Subject $subject */
        $subject = Mage::getModel('core/email_template_filter');
        self::assertSame('', $subject->layoutDirective(['{{layout handle="default"}}', 'layout', ' handle="default"']));
        self::assertSame('', $subject->layoutDirective(['{{layout}}', 'layout', '']));
    }
}
