<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
