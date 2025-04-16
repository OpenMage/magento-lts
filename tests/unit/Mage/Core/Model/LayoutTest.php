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

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Error;
use Generator;
use Mage;
use Mage_Core_Block_Abstract;
use Mage_Core_Model_Layout as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\LayoutTrait;
use OpenMage\Tests\Unit\Traits\PhpStormMetaData\BlocksTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class LayoutTest extends OpenMageTest
{
    use BlocksTrait;
    use LayoutTrait;

    private static Subject $subject;

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
            static::assertInstanceOf($expectedResult, $result);
        } else {
            static::assertFalse($result);
        }
    }

    /**
     * @covers Mage_Core_Model_Layout::getBlockSingleton()
     * @dataProvider provideGetBlockSingleton
     * @group Model
     * @group pr4411
     *
     * @param class-string $expectedResult
     */
    public function testGetBlockSingleton(string $expectedResult, bool $isAbstractBlock, string $type): void
    {
        $result = self::$subject->getBlockSingleton($type);

        static::assertInstanceOf($expectedResult, $result);

        if ($isAbstractBlock) {
            static::assertInstanceOf(Mage_Core_Block_Abstract::class, $result);
        } else {
            static::assertNotInstanceOf(Mage_Core_Block_Abstract::class, $result);
        }
    }

    public function provideGetBlockSingleton(): Generator
    {
        $notInstanceOfMageCoreBlockAbstract = $this->getBlockClassesNotInstanceOfMageCoreBlockAbstract();

        $ignoredClasses = array_merge(
            $this->getAbstractBlockClasses(),
            $this->getBlockClassesToMock(),
            $this->getBlockClassesWithErrors(),
            $this->getBlockClassesWithSessions(),
        );

        #$allBlocks = $this->getAllBlockClasses();
        $allBlocks = [
            'adminhtml/api_buttons' => \Mage_Adminhtml_Block_Api_Buttons::class,
            'adminhtml/catalog_category_helper_pricestep' => \Mage_Adminhtml_Block_Catalog_Category_Helper_Pricestep::class,
        ];

        foreach ($allBlocks as $alias => $className) {
            if (!in_array($className, $ignoredClasses)) {
                yield $className => [
                    $className,
                    !in_array($className, $notInstanceOfMageCoreBlockAbstract),
                    $alias,
                ];
            }
        }
    }

    /**
     * @covers Mage_Core_Model_Layout::getBlockSingleton()
     * @group Model
     */
    public function testGetBlockSingletonError(): void
    {
        $this->expectException(Error::class);
        if (PHP_VERSION_ID >= 80000) {
            $this->expectExceptionMessage('Class "Mage_Invalid_Block_Type" not found');
        } else {
            $this->expectExceptionMessage("Class 'Mage_Invalid_Block_Type' not found");
        }

        self::$subject->getBlockSingleton('invalid/type');
    }
}
