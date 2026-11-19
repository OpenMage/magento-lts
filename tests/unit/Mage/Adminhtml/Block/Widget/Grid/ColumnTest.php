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

    /**
     * A static ["Class", "method"] frame callback, the only kind layout XML can express, is not called
     *
     * @group Block
     */
    public function testStaticFrameCallbackIsNotCalled(): void
    {
        $column = $this->createColumn();
        $column->setFrameCallback($this->staticCallbackArray());

        self::$staticCalls = 0;

        self::assertSame('cell', $column->getRowField(new Varien_Object(['value' => 'cell'])));
        self::assertSame('cell', $column->getRowFieldExport(new Varien_Object(['value' => 'cell'])));
        self::assertSame(0, self::$staticCalls);

        $column->setFrameCallback(['Zend_Loader', 'loadFile']);
        self::assertSame('x', $column->getRowFieldExport(new Varien_Object(['value' => 'x'])));
    }

    /**
     * @group Block
     */
    public function testObjectFrameCallbackIsCalled(): void
    {
        $column = $this->createColumn();
        $decorator = new class {
            public function decorate(string $value, Varien_Object $row, Subject $column, bool $isExport): string
            {
                return '[' . $value . ($isExport ? '|export' : '') . ']';
            }
        };

        // the [object, method] array form that grids built before first-class callables use
        $method = 'decorate';
        $column->setFrameCallback([$decorator, $method]);
        self::assertSame('[cell]', $column->getRowField(new Varien_Object(['value' => 'cell'])));
        self::assertSame('[cell|export]', $column->getRowFieldExport(new Varien_Object(['value' => 'cell'])));

        $column->setFrameCallback(static fn(string $value): string => '<' . $value . '>');
        self::assertSame('<cell>', $column->getRowField(new Varien_Object(['value' => 'cell'])));
    }

    /**
     * @group Block
     */
    public function testGetterCallables(): void
    {
        $column = $this->createColumn();
        $row = new Varien_Object(['value' => 'cell']);

        $column->setGetter('getValue');
        self::assertSame('cell', $column->getRowFieldExport($row));

        $column->setGetter(static fn(Varien_Object $row): string => 'closure:' . $row->getValue());
        self::assertSame('closure:cell', $column->getRowFieldExport($row));

        $column->setGetter($this->staticCallbackArray());
        self::$staticCalls = 0;
        self::assertSame('', $column->getRowFieldExport($row));
        self::assertSame(0, self::$staticCalls);
    }

    public static int $staticCalls = 0;

    public static function staticFrameCallback(mixed ...$args): string
    {
        self::$staticCalls++;
        return 'called';
    }

    private function createColumn(): Subject
    {
        $column = new Subject();
        $column->setIndex('value');

        $renderer = new class extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
            #[Override]
            public function render(Varien_Object $row)
            {
                return (string) $this->_getValue($row);
            }
        };
        $renderer->setColumn($column);

        $column->setRenderer($renderer);

        return $column;
    }

    /**
     * The static ["Class", "method"] array that layout XML can express. The method name is passed
     * through a variable so that Rector's ArrayToFirstClassCallableRector does not turn the array
     * into a closure, which the column is meant to allow.
     *
     * @return array{class-string, string}
     */
    private function staticCallbackArray(string $method = 'staticFrameCallback'): array
    {
        return [self::class, $method];
    }
}
