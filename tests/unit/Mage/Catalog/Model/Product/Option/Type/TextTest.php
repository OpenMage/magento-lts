<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Product\Option\Type;

use Generator;
use Mage;
use Mage_Catalog_Model_Product_Option;
use Mage_Catalog_Model_Product_Option_Type_Text as Subject;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/product_option_type_text');
    }

    /**
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testValidateUserValue(): void
    {
        $this->subject->setOption(new Mage_Catalog_Model_Product_Option());
        $this->assertInstanceOf(Subject::class, $this->subject->validateUserValue([]));
    }


    /**
     * @dataProvider providePrepareForCart
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testPrepareForCart($expectedResult, bool $setIsValid = true, $setUserValue = null): void
    {
        $this->subject->setIsValid($setIsValid)->setUserValue($setUserValue);
        $this->assertSame($expectedResult, $this->subject->prepareForCart());
    }

    public function providePrepareForCart(): Generator
    {
        yield 'valid' => [
            'test',
            true,
            'test',
        ];
        yield 'invalid' => [
            null,
        ];
    }

    /**
     * @covers Mage_Catalog_Model_Product_Option_Type_Text::getFormattedOptionValue()
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        $this->assertIsString($this->subject->getFormattedOptionValue(''));
    }
}
