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
use OpenMage\Tests\Unit\OpenMageTest;

class TextTest extends OpenMageTest
{
    private static Subject $subject;

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
        static::assertInstanceOf(Subject::class, self::$subject->validateUserValue([]));
    }


    /**
     * @dataProvider providePrepareForCart
     * @group Model
     */
    public function testPrepareForCart(?string $expectedResult, bool $setIsValid = true, ?string $setUserValue = null): void
    {
        self::$subject->setIsValid($setIsValid)->setUserValue($setUserValue);
        static::assertSame($expectedResult, self::$subject->prepareForCart());
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
     * @group Model
     */
    public function testGetDefaultAttributeSetId(): void
    {
        static::assertIsString(self::$subject->getFormattedOptionValue(''));
    }
}
