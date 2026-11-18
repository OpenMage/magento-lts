<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model\Widget;

use Generator;

trait InstanceTrait
{
    /**
     * @return Generator<string, array{string}>
     */
    public static function provideUndeclaredTypeData(): Generator
    {
        yield 'block that is not a widget' => ['core/text_list'];
        yield 'admin block' => ['adminhtml/template'];
        yield 'declared type followed by an attribute break-out' => ['cms/widget_page_link" x="y'];
        yield 'declared type followed by markup' => ['cms/widget_page_link"><block type="core/text'];
    }

    /**
     * @return Generator<string, array{string, mixed, string}>
     */
    public static function provideInvalidPageGroupData(): Generator
    {
        $valid = ['page_id' => 0, 'for' => 'all', 'layout_handle' => 'default', 'block' => 'content', 'template' => ''];
        $displayError = 'Wrong widget display option specified.';
        $charsError = 'Templatepath or block reference contain special characters.';

        yield 'unknown page group' => ['unknown_group', $valid, $displayError];
        yield 'page group data is not an array' => ['all_pages', 'content', $displayError];
        yield 'unknown display-for value' => ['all_pages', ['for' => 'everything'] + $valid, $displayError];
        yield 'specific entities on a page group without entities' => ['all_pages', ['for' => 'specific'] + $valid, $displayError];
        yield 'quote in block reference' => ['all_pages', ['block' => 'content"'] + $valid, $charsError];
        yield 'markup in block reference' => ['all_pages', ['block' => 'content><x'] + $valid, $charsError];
        yield 'block reference is an array' => ['all_pages', ['block' => ['content']] + $valid, $charsError];
        yield 'quote in template' => ['all_pages', ['template' => 'cms/widget/link/link_block.phtml"'] + $valid, $charsError];
    }

    /**
     * @return Generator<string, array{string, string, bool}>
     */
    public static function provideStoredLayoutUpdateXmlData(): Generator
    {
        $type = 'cms/widget_page_link';
        $action = '<action method="setData"><name>anchor_text</name><value>Say &quot;hi&quot; &amp; bye</value></action>';
        $block = static fn(string $attributes, string $inner = ''): string => '<reference name="content"><block type="' . $type . '" name="abc123"' . $attributes . '>' . $inner . '</block></reference>';

        yield 'empty update' => [$type, '', true];
        yield 'whitespace only' => [$type, "  \n", true];
        yield 'block without parameters' => [$type, $block(''), true];
        yield 'block with template and parameters' => [$type, $block(' template="cms/widget/link/link_block.phtml"', $action . $action), true];
        yield 'CDATA value' => [$type, $block('', '<action method="setData"><name>title</name><value><![CDATA[a < b]]></value></action>'), true];
        yield 'whitespace between elements' => [$type, "<reference name=\"content\">\n  <block type=\"{$type}\" name=\"abc\">\n    {$action}\n  </block>\n</reference>", true];

        yield 'undeclared instance type' => ['core/text_list', '<reference name="content"><block type="core/text_list" name="abc"/></reference>', false];
        yield 'block type differs from instance type' => [$type, '<reference name="content"><block type="core/text_list" name="abc"/></reference>', false];
        yield 'malformed XML' => [$type, '<reference name="content"><block type="' . $type . '" name="abc">', false];
        yield 'two references' => [$type, $block('') . $block(''), false];
        yield 'two blocks' => [$type, '<reference name="content"><block type="' . $type . '" name="a"/><block type="' . $type . '" name="b"/></reference>', false];
        yield 'nested block' => [$type, $block('', '<block type="' . $type . '" name="nested"/>'), false];
        yield 'extra block attribute' => [$type, $block(' foo="bar"'), false];
        yield 'extra reference attribute' => [$type, '<reference name="content" foo="bar"><block type="' . $type . '" name="abc"/></reference>', false];
        yield 'action other than setData' => [$type, $block('', '<action method="setTemplate"><name>x</name><value>y</value></action>'), false];
        yield 'attribute on an action argument' => [$type, $block('', '<action method="setData"><name foo="bar">x</name><value>y</value></action>'), false];
        yield 'markup inside an action argument' => [$type, $block('', '<action method="setData"><name>x</name><value><b>y</b></value></action>'), false];
        yield 'missing action argument' => [$type, $block('', '<action method="setData"><name>x</name></action>'), false];
        yield 'text next to elements' => [$type, $block('', 'text' . $action), false];
        yield 'comment' => [$type, $block('', '<!-- x -->'), false];
        yield 'namespaced element' => [$type, '<reference name="content"><x:block xmlns:x="urn:x" type="' . $type . '" name="abc"/></reference>', false];
        yield 'other layout instruction' => [$type, '<remove name="content"/>', false];
        yield 'block without name' => [$type, '<reference name="content"><block type="' . $type . '"/></reference>', false];
        yield 'block with empty name' => [$type, '<reference name="content"><block type="' . $type . '" name=""/></reference>', false];
        yield 'reference without name' => [$type, '<reference><block type="' . $type . '" name="abc"/></reference>', false];
        yield 'escaped quote in reference name' => [$type, '<reference name="content&quot;"><block type="' . $type . '" name="abc"/></reference>', false];
        yield 'escaped quote in template' => [$type, $block(' template="cms/widget/link/link_block.phtml&quot;"'), false];
        yield 'template set through a parameter' => [$type, $block('', '<action method="setData"><name>template</name><value>page/html/header.phtml</value></action>'), false];
        yield 'parameter without a name' => [$type, $block('', '<action method="setData"><name/><value>y</value></action>'), false];
    }

    /**
     * Parameter values the generator must escape so that its output is well-formed and round-trips
     *
     * @return Generator<string, array{string, string}>
     */
    public static function provideWidgetParameterValueData(): Generator
    {
        yield 'HTML named entity' => ['Free&nbsp;shipping', 'Free&nbsp;shipping'];
        yield 'XML entity' => ['Tom &amp; Jerry', 'Tom &amp; Jerry'];
        yield 'numeric character reference' => ['&#60;b&#62;', '&#60;b&#62;'];
        yield 'quotes' => ['Say "hi" and \'bye\'', 'Say "hi" and \'bye\''];
        yield 'ampersand' => ['Fish & chips', 'Fish & chips'];
        yield 'markup' => ['<b>bold</b> </value></action><action method="setTemplate">', '<b>bold</b> </value></action><action method="setTemplate">'];
        yield 'CDATA end' => [']]>', ']]>'];
        yield 'unicode' => ['Grüße, 日本語, emoji 🎉', 'Grüße, 日本語, emoji 🎉'];
        yield 'line breaks and tabs' => ["a\tb\r\nc", "a\tb\nc"];
        yield 'control character' => ["a\x01b", "a\u{FFFD}b"];
        yield 'invalid UTF-8' => ["a\xC3b", "a\u{FFFD}b"];
    }

    /**
     * @return Generator<string, array{string, string, null|string}>
     */
    public static function provideLayoutUpdateRemovalReasonData(): Generator
    {
        $type = 'cms/widget_page_link';
        $generated = '<reference name="content"><block type="' . $type . '" name="abc"/></reference>';

        yield 'generated update of a declared type' => [$type, $generated, null];
        yield 'empty update of a declared type' => [$type, '', null];
        yield 'empty update of an undeclared type' => ['core/text_list', '', null];
        yield 'other XML on a declared type' => [$type, '<remove name="content"/>', 'unexpected layout update XML'];
        yield 'malformed XML on a declared type' => [$type, '<reference name="content">', 'unexpected layout update XML'];
        yield 'undeclared type' => ['core/text_list', '<reference name="content"><block type="core/text_list" name="abc"/></reference>', 'undeclared widget type'];
    }
}
