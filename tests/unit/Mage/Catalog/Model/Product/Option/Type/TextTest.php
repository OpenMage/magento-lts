<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Option\Type;

use Override;
use Mage;
use Mage_Catalog_Model_Product_Option;
use Mage_Catalog_Model_Product_Option_Type_Text as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Product\Option\Type\TextTrait;

final class TextTest extends OpenMageTest
{
    use TextTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalog/product_option_type_text');
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testValidateUserValue(): void
    {
        self::$subject->setOption(new Mage_Catalog_Model_Product_Option());
        self::assertInstanceOf(Subject::class, self::$subject->validateUserValue([]));
    }


    /**
     * @dataProvider providePrepareForCart
     * @group Model
     */
    public function testPrepareForCart(?string $expectedResult, bool $setIsValid = true, ?string $setUserValue = null): void
    {
        self::$subject->setIsValid($setIsValid)->setUserValue($setUserValue);
        self::assertSame($expectedResult, self::$subject->prepareForCart());
    }

    /**
     * @covers Mage_Catalog_Model_Product_Option_Type_Text::getFormattedOptionValue()
     * @group Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        self::assertIsString(self::$subject->getFormattedOptionValue(''));
    }

    /**
     * Browsers post textarea content with CRLF; the JS validator counts each
     * line break as one character. The server must agree, otherwise input that
     * passed client-side validation is rejected with "The text is too long".
     *
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testValidateUserValueNormalizesCrlfWithinMaxLength(): void
    {
        $option = new Mage_Catalog_Model_Product_Option();
        $option->setId('opt');
        $option->setMaxCharacters(7);

        self::$subject->setOption($option);
        self::$subject->validateUserValue(['opt' => "abcd\r\nef"]);

        self::assertTrue(self::$subject->getIsValid());
        self::assertSame("abcd\nef", self::$subject->getUserValue());
    }

    /**
     * @group Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testValidateUserValueRejectsOverMaxAfterCrlfNormalization(): void
    {
        $option = new Mage_Catalog_Model_Product_Option();
        $option->setId('opt');
        $option->setMaxCharacters(5);

        self::$subject->setOption($option);

        $this->expectException(Mage_Core_Exception::class);
        self::$subject->validateUserValue(['opt' => "abcd\r\nef"]);
    }
}
