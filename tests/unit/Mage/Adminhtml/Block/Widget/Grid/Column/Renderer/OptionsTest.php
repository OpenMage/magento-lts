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
use Mage_Adminhtml_Block_Widget_Grid_Column;
use Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select;
use Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options as Subject;
use Mage_Core_Helper_Data;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Varien_Object;

final class OptionsTest extends TestCase
{
    private mixed $previousCoreHelper;

    #[Override]
    protected function setUp(): void
    {
        $this->previousCoreHelper = Mage::registry('_helper/core');
        Mage::unregister('_helper/core');
        Mage::register('_helper/core', new Mage_Core_Helper_Data());
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_helper/core');
        if ($this->previousCoreHelper !== null) {
            Mage::register('_helper/core', $this->previousCoreHelper);
        }
    }

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
        $column = new Mage_Adminhtml_Block_Widget_Grid_Column([
            'index' => 'method',
            'options' => ['checkmo' => 'Check'],
            'show_missing_option_values' => true,
        ]);
        $subject = new Subject();
        $subject->setColumn($column);
        self::assertSame('removed_method', $subject->render(new Varien_Object(['method' => 'removed_method'])));

        $filter = new Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select();
        $filter->setColumn($column);
        self::assertSame([
            ['value' => null, 'label' => ''],
            ['value' => 'checkmo', 'label' => 'Check'],
        ], (new ReflectionMethod($filter, '_getOptions'))->invoke($filter));
    }
}
