<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
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

final class LayoutTest extends OpenMageTest
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
        $this->expectExceptionMessage('Class "Mage_Invalid_Block_Type" not found');

        self::$subject->getBlockSingleton('invalid/type');
    }
}
