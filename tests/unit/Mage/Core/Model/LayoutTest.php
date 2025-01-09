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
use Mage_Core_Model_Layout;
use OpenMage\Tests\Unit\Traits\PhpStormMetaData\BlocksTrait;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    use BlocksTrait;

    public Mage_Core_Model_Layout $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/layout');
    }

    /**
     * @dataProvider provideCreateBlock
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testCreateBlock($expectedResult, bool $willReturnBlock, string $type, ?string $name, array $attributes): void
    {
        $result = $this->subject->createBlock($type, $name, $attributes);

        if ($willReturnBlock) {
            $this->assertInstanceOf($expectedResult, $result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function provideCreateBlock(): Generator
    {
        yield 'instance of Mage_Core_Block_Abstract' => [
            \Mage_Cms_Block_Block::class,
            true,
            'cms/block',
            null,
            [],
        ];
        yield 'not instance of Mage_Core_Block_Abstract' => [
            false,
            false,
            'rule/conditions',
            null,
            [],
        ];
    }

    /**
     * @covers Mage_Core_Model_Layout::getBlockSingleton()
     * @dataProvider provideGetBlockSingleton
     * @group Mage_Core
     * @group Mage_Core_Model
     * @group pr4411
     */
    public function testGetBlockSingleton($expectedResult, bool $isAbstractBlock, string $type): void
    {
        $result = $this->subject->getBlockSingleton($type);

        $this->assertInstanceOf($expectedResult, $result);

        if ($isAbstractBlock) {
            $this->assertInstanceOf(\Mage_Core_Block_Abstract::class, $result);
        } else {
            $this->assertNotInstanceOf(\Mage_Core_Block_Abstract::class, $result);
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
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testGetBlockSingletonError(): void
    {
        $this->expectException(Error::class);
        if (PHP_VERSION_ID >= 80000) {
            $this->expectExceptionMessage('Class "Mage_Invalid_Block_Type" not found');
        } else {
            $this->expectExceptionMessage("Class 'Mage_Invalid_Block_Type' not found");
        }

        $this->subject->getBlockSingleton('invalid/type');
    }
}
