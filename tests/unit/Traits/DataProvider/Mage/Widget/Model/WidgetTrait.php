<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model;

use Generator;

trait WidgetTrait
{
    /**
     * @return Generator<string, array{string, bool}>
     */
    public static function provideWidgetTypeData(): Generator
    {
        yield 'declared core widget' => ['cms/widget_page_link', true];
        yield 'declared catalog widget' => ['catalog/product_widget_new', true];
        yield 'empty' => ['', false];
        yield 'block that is not a widget' => ['core/text_list', false];
        yield 'admin block' => ['adminhtml/template', false];
        yield 'class name instead of alias' => ['Mage_Cms_Block_Widget_Page_Link', false];
        yield 'declared type with trailing text' => ['cms/widget_page_link ', false];
        yield 'double quote that would close an XPath string' => ['core/text" or "1"="1', false];
        yield 'XPath expression matching any widget' => ['x" or @type!="', false];
        yield 'single quote' => ["cms/widget_page_link' or '1'='1", false];
    }
}
