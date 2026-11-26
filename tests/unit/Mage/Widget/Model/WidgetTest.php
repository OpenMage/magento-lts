<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Widget\Model;

use Mage;
use Mage_Widget_Model_Widget as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model\WidgetTrait;
use Override;
use Varien_Simplexml_Config;
use Varien_Simplexml_Element;

final class WidgetTest extends OpenMageTest
{
    use WidgetTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('widget/widget');
    }

    /**
     * Only a type declared in a widget.xml resolves; the type is never evaluated as XPath.
     *
     * @dataProvider provideWidgetTypeData
     * @group Model
     */
    public function testGetXmlElementByType(string $type, bool $declared): void
    {
        $element = self::$subject->getXmlElementByType($type);

        if ($declared) {
            self::assertInstanceOf(Varien_Simplexml_Element::class, $element);
            self::assertSame($type, $element->getAttribute('type'));
        } else {
            self::assertNull($element);
        }

        self::assertSame($declared, self::$subject->isWidgetTypeDeclared($type));
    }

    /**
     * Each widget type is looked up once, until the answers are reset.
     *
     * @group Model
     */
    public function testDeclaredTypesAreReadOnce(): void
    {
        $subject = $this->createPartialMock(Subject::class, ['getXmlConfig']);
        $subject->expects(self::exactly(3))->method('getXmlConfig')
            ->willReturn(new Varien_Simplexml_Config('<widgets><a type="cms/widget_page_link"/><b type="catalog/product_widget_new"/></widgets>'));

        foreach ([1, 2] as $check) {
            self::assertTrue($subject->isWidgetTypeDeclared('cms/widget_page_link'), "check $check");
            self::assertFalse($subject->isWidgetTypeDeclared('core/text_list'), "check $check");
        }

        $subject->resetDeclaredTypes();
        self::assertTrue($subject->isWidgetTypeDeclared('cms/widget_page_link'));
    }
}
