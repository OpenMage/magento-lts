<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Widget\Grid\Column\Renderer;

use Generator;
use Mage;
use Mage_Adminhtml_Block_Widget_Grid;
use Mage_Adminhtml_Block_Widget_Grid_Column;
use Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select;
use Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;
use Varien_Object;

final class OptionsTest extends OpenMageTest
{
    /** @param array<string, string> $options */
    #[DataProvider('provideRender')]
    public function testRender(array $options, mixed $value, bool $showMissing, string $expected): void
    {
        $column = new Mage_Adminhtml_Block_Widget_Grid_Column([
            'index' => 'method',
            'options' => $options,
            'show_missing_option_values' => $showMissing,
        ]);
        $subject = new Subject();
        $subject->setColumn($column);

        $row = new Varien_Object(['method' => $value]);

        self::assertSame($expected, $subject->render($row));
        self::assertSame($expected, $subject->renderExport($row));
        self::assertSame($options, $column->getOptions());
    }

    public static function provideRender(): Generator
    {
        yield 'configured label' => [['checkmo' => 'Check & Money'], 'checkmo', true, 'Check &amp; Money'];
        yield 'missing code' => [['checkmo' => 'Check'], 'removed_method', true, 'removed_method'];
        yield 'opt in required' => [['checkmo' => 'Check'], 'removed_method', false, ''];
        yield 'no methods left' => [[], 'removed_method', true, 'removed_method'];
        yield 'empty options without opt in' => [[], 'removed_method', false, ''];
        yield 'escaped code' => [[], '<removed&method>', true, '&lt;removed&amp;method&gt;'];
        yield 'null remains blank' => [[], null, true, ''];
        yield 'array of codes' => [['checkmo' => 'Check'], ['checkmo', 'removed_method'], true, 'Check, removed_method'];
        yield 'array without options' => [[], ['old_method', '<removed>'], true, 'old_method, &lt;removed&gt;'];
        yield 'array without opt in' => [[], ['old_method'], false, ''];
        yield 'legacy label matching' => [['checkmo' => 'Check'], 'Check', false, 'Check'];
    }

    public function testMissingCodesAreNotAddedToFilterOptions(): void
    {
        $grid = new Mage_Adminhtml_Block_Widget_Grid();
        $grid->setLayout(Mage::app()->getLayout());
        $grid->addColumn('method', [
            'index' => 'method',
            'type' => 'options',
            'options' => ['checkmo' => 'Check'],
            'show_missing_option_values' => true,
        ]);
        $column = $grid->getColumn('method');
        self::assertInstanceOf(Mage_Adminhtml_Block_Widget_Grid_Column::class, $column);
        self::assertSame('removed_method', $column->getRenderer()->render(new Varien_Object(['method' => 'removed_method'])));

        $filter = $column->getFilter();
        self::assertInstanceOf(Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select::class, $filter);
        $html = $filter->getHtml();
        self::assertStringContainsString('<option value="checkmo">Check</option>', (string) $html);
        self::assertStringNotContainsString('removed_method', (string) $html);
        self::assertSame(2, substr_count($html, '<option '));
    }
}
