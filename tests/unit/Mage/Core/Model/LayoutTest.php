<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Error;
use Mage;
use Mage_Core_Block_Abstract;
use Mage_Core_Block_Text;
use Mage_Core_Model_Layout as Subject;
use Mage_Core_Model_Layout_Element;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\LayoutTrait;
use OpenMage\Tests\Unit\Traits\PhpStormMetaData\BlocksTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;

final class LayoutTest extends OpenMageTest
{
    use BlocksTrait;

    use LayoutTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/layout');
    }

    /**
     * @dataProvider provideCreateBlock
     * @group Model
     *
     * @param bool|class-string $expectedResult
     */
    public function testCreateBlock(bool|string $expectedResult, bool $willReturnBlock, string $type, ?string $name, array $attributes): void
    {
        $result = self::$subject->createBlock($type, $name, $attributes);

        if ($willReturnBlock && is_string($expectedResult)) {
            self::assertInstanceOf($expectedResult, $result);
        } else {
            self::assertFalse($result);
        }
    }

    /**
     * @covers Mage_Core_Model_Layout::getBlockSingleton()
     * @dataProvider provideGetBlockSingleton
     * @group Model
     *
     * @param class-string $expectedResult
     */
    public function testGetBlockSingleton(string $expectedResult, bool $isAbstractBlock, string $type): void
    {
        $result = self::$subject->getBlockSingleton($type);

        self::assertInstanceOf($expectedResult, $result);

        if ($isAbstractBlock) {
            self::assertInstanceOf(Mage_Core_Block_Abstract::class, $result);
        } else {
            self::assertNotInstanceOf(Mage_Core_Block_Abstract::class, $result);
        }
    }

    /**
     * @covers Mage_Core_Model_Layout::getBlockSingleton()
     * @group Model
     */
    public function testGetBlockSingletonError(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Class "Mage_Invalid_Block_Type" not found');

        self::$subject->getBlockSingleton('invalid/type');
    }

    /**
     * Regression test for https://github.com/OpenMage/magento-lts/pull/5670
     *
     * On PHP 8+, call_user_func_array() treats a string-keyed array as named arguments.
     * The helper argument array built from the layout XML child node names (e.g. "value", "len")
     * does not match the helper method's parameter names (e.g. "$string", "$length"), so passing
     * it directly used to throw "Unknown named parameter". Wrapping it in array_values() restores
     * the pre-PHP8 positional-argument behavior.
     *
     * @covers Mage_Core_Model_Layout::_generateAction()
     * @group Model
     */
    public function testGenerateActionWithHelperCallbackArgument(): void
    {
        $block = self::$subject->createBlock('core/text', 'generateActionHelperCallbackTestBlock');
        self::assertInstanceOf(Mage_Core_Block_Text::class, $block);

        $xml = <<<'XML'
            <action method="setText" block="generateActionHelperCallbackTestBlock">
                <text helper="core/string/truncate">
                    <value>Hello World Long String</value>
                    <len>10</len>
                </text>
            </action>
            XML;

        $node = new Mage_Core_Model_Layout_Element($xml);

        (new ReflectionMethod(Subject::class, '_generateAction'))->invoke(self::$subject, $node, $node);

        self::assertSame('Hello W...', $block->getText());
    }
}
