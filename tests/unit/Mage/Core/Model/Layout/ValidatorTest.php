<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Layout;

use Override;
use Generator;
use Mage;
use Mage_Core_Model_Layout_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\UrlTrait;

final class ValidatorTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/layout_validator');
    }

    /**
     * @dataProvider isValidDataProvider
     * @group Model
     * @param array<string, string> $expectedErrors
     */
    public function testIsValid(bool $expected, string $value, array $expectedErrors): void
    {
        // the layout generator only instantiates block classes that are already loaded or on the include path
        self::assertTrue(class_exists(ValidatorTestZendBlockFixture::class));

        // fresh instance: validator messages accumulate across isValid() calls
        $subject = Mage::getModel('core/layout_validator');
        self::assertSame($expected, $subject->isValid($value));

        if (!$expected) {
            $messages = $subject->getMessages();
            self::assertIsArray($messages);
            self::assertSame($expectedErrors, $messages);
        }
    }

    public function isValidDataProvider(): Generator
    {
        yield 'valid string' => [
            true,
            'default',
            [],
        ];

        yield 'invalid string' => [
            false,
            '<invalid-node>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'plain action without helper' => [
            true,
            '<reference name="content"><action method="setText"><text>x</text></action></reference>',
            [],
        ];

        yield 'helper attribute' => [
            false,
            '<reference name="content"><action method="setText"><text helper="core/string/truncate"/></action></reference>',
            [
                'protectedAttrHelperInActionVar' => 'Helper attributes should not be used in custom layout updates.',
            ],
        ];

        yield 'helper attribute under default-namespaced action (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action xmlns="urn:x" method="setText"><text helper="core/string/truncate"/></action></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'default namespace on outer element (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference xmlns="urn:x" name="content"><action method="setText"><text>x</text></action></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'prefixed namespace element' => [
            false,
            '<reference xmlns:n="urn:x" name="content"><n:action method="setText"/></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'namespaced attribute' => [
            false,
            '<reference xmlns:n="urn:x" name="content" n:foo="bar"/>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'template path traversal' => [
            false,
            '<reference name="content"><block type="core/template" name="t" template="../x.phtml"/></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'setTemplate method name in lower case (PHP method names are case-insensitive) (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="settemplate"><t>../x.phtml</t></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'setTemplate method name in upper case (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="SETTEMPLATE"><t>../x.phtml</t></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'setTemplate as a Class::method callable (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="Mage_Core_Block_Template::setTemplate"><t>../x.phtml</t></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'setDataUsingMethod method name in mixed case (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="SetDataUsingMethod"><k>Template</k><v>../x.phtml</v></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'JSON-escaped traversal in a json argument (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="setTemplate" json="t"><t>"\\u002e\\u002e/x.phtml"</t></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'traversal in a later setTemplate argument (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action method="setTemplate"><a>page/1column.phtml</a><b>../x.phtml</b></action></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];

        yield 'lower-case setTemplate with a safe path (GHSA-v2qw-3rm9-qx3p)' => [
            true,
            '<reference name="content"><action method="settemplate"><t>page/1column.phtml</t></action></reference>',
            [],
        ];

        yield 'disallowed block by class name in another case (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="mage_core_block_template_zend" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'disallowed block by module alias (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="core/template_zend" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'disallowed block by module alias with a capitalised module (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="Core/template_zend" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'disallowed block by class name with a leading backslash (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="\\Mage_Core_Block_Template_Zend" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'disallowed block in the class attribute, which wins over type (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="core/text" class="Mage_Core_Block_Template_Zend" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'subclass of a disallowed block (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<block type="OpenMage\\Tests\\Unit\\Mage\\Core\\Model\\Layout\\ValidatorTestZendBlockFixture" name="z"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'allowed block by alias (GHSA-v2qw-3rm9-qx3p)' => [
            true,
            '<block type="core/text" name="t"/>',
            [],
        ];

        $grid = '<reference name="content"><block type="%s" name="g">'
            . '<action method="addColumn" json="column"><id>x</id>'
            . '<column>{"index":"x","default":"x","frame_callback":["Zend_Loader","loadFile"]}</column></action>'
            . '<action method="getCsv"/></block></reference>';

        yield 'admin grid with a JSON column callback' => [
            false,
            sprintf($grid, 'adminhtml/catalog_product_grid'),
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin CMS page grid with a JSON column callback' => [
            false,
            sprintf($grid, 'adminhtml/cms_page_grid'),
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block by alias' => [
            false,
            '<block type="adminhtml/customer_grid" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block by alias with a capitalised module' => [
            false,
            '<block type="Adminhtml/customer_grid" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block by class name in another case' => [
            false,
            '<block type="\\mage_adminhtml_block_customer_grid" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block in the class attribute' => [
            false,
            '<block type="core/text" class="Mage_Adminhtml_Block_Widget_Grid_Column" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block of another module (Block_Adminhtml naming)' => [
            false,
            '<block type="sales/adminhtml_recurring_profile_grid" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'block extending an admin block base class' => [
            false,
            '<block type="weee/renderer_weee_tax" name="g"/>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'admin block nested in a frontend block' => [
            false,
            '<reference name="content"><block type="core/text_list" name="l">'
                . '<block type="adminhtml/cms_page_grid" name="g"/></block></reference>',
            [
                'invalidBlockName' => 'Disallowed block name for frontend.',
            ],
        ];

        yield 'json attribute on a frontend block action' => [
            false,
            '<reference name="content"><block type="core/text" name="t">'
                . '<action method="setData" json="v"><k>x</k><v>["Zend_Loader","loadFile"]</v></action></block></reference>',
            [
                'protectedAttrJsonInAction' => 'JSON arguments should not be used in custom layout updates.',
            ],
        ];

        yield 'json attribute on an action of an existing block' => [
            false,
            '<reference name="content"><action method="setData" json="v"><k>x</k><v>"y"</v></action></reference>',
            [
                'protectedAttrJsonInAction' => 'JSON arguments should not be used in custom layout updates.',
            ],
        ];

        yield 'frontend block with a plain setData action' => [
            true,
            '<reference name="content"><block type="catalog/product_list" name="p">'
                . '<action method="setData"><k>column_count</k><v>4</v></action></block></reference>',
            [],
        ];

        yield 'frontend block whose name mentions admin' => [
            true,
            '<reference name="content"><block type="core/text" name="adminhtml_grid"/></reference>',
            [],
        ];
    }
}
