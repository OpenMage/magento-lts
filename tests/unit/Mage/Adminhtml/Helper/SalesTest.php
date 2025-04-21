<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper;

use Generator;
use Mage;
use Mage_Adminhtml_Helper_Sales as Subject;
use PHPUnit\Framework\TestCase;

class SalesTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('adminhtml/sales');
    }

    /**
     * @covers Mage_Adminhtml_Helper_Sales::escapeHtmlWithLinks()
     * @dataProvider provideDecodeGridSerializedInput
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Helper
     */
    public function testEscapeHtmlWithLinks($expectedResult, $data): void
    {
        $this->assertSame($expectedResult, $this->subject->escapeHtmlWithLinks($data, ['a']));
    }

    public function provideDecodeGridSerializedInput(): Generator
    {
        yield 'test #1' => [
            '&lt;a href=&quot;https://localhost&quot;&gt;',
            '<a href="https://localhost">',
        ];
    }
}
