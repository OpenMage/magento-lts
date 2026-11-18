<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget\Grid;

use DOMDocument;
use Generator;
use Mage_Adminhtml_Block_Widget_Grid_Column as Subject;
use Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract;
use Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text;
use OpenMage\Tests\Unit\OpenMageTest;
use Override;
use Varien_Object;

final class ColumnTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @group Block
     */
    public function testGetType(): void
    {
        self::assertSame('', self::$subject->getType());

        self::$subject->setType('text');
        self::assertSame('text', self::$subject->getType());
    }

    /**
     * The copy-to-clipboard value must be escaped in the attribute, and the
     * browser must decode it back to the original value for the copy handler.
     *
     * The base renderer returns the raw value from getCopyableText(); the text
     * renderer returns it already escaped, so escaping must not double-encode.
     *
     * @dataProvider provideGetRowFieldCopyableData
     * @group Block
     */
    public function testGetRowFieldCopyableEscapesAttribute(string $rendererType, string $value): void
    {
        $column = new Subject();
        $column->setIndex('value');
        $column->setCopyable(true);

        $renderer = $rendererType === 'text'
            ? new Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text()
            : new class extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
                #[Override]
                public function render(Varien_Object $row)
                {
                    return 'cell';
                }
            };
        $renderer->setColumn($column);

        $column->setRenderer($renderer);

        $html = $column->getRowField(new Varien_Object(['value' => $value]));

        self::assertStringStartsWith('<span data-copy-text="', $html);

        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><body>' . $html . '</body>', LIBXML_NOERROR);

        $body = $dom->getElementsByTagName('body')->item(0);
        $spans = $dom->getElementsByTagName('span');

        self::assertNotNull($body);
        self::assertSame(1, $body->childNodes->length);
        self::assertSame(1, $spans->length);
        $span = $spans->item(0);
        self::assertNotNull($span);
        self::assertSame(['data-copy-text'], array_map(
            static fn($attr) => $attr->name,
            iterator_to_array($span->attributes, false),
        ));
        self::assertSame($value, $span->getAttribute('data-copy-text'));
        self::assertSame(0, $dom->getElementsByTagName('svg')->length);
    }

    public static function provideGetRowFieldCopyableData(): Generator
    {
        $values = [
            'plain' => '100000001',
            'email' => 'john.doe@example.com',
            'attribute breakout' => '"><svg onload=alert(1)>@a.b',
            'event handler' => 'x" onmouseover="alert(1)@a.b',
            'single quote and ampersand' => "o'brien&co@example.com",
        ];
        foreach (['raw', 'text'] as $rendererType) {
            foreach ($values as $label => $value) {
                yield $rendererType . ' renderer, ' . $label => [$rendererType, $value];
            }
        }
    }
}
