<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block;

use Mage_Adminhtml_Block_Template as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;

class TemplateTest extends OpenMageTest
{
    use CoreTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @see Mage_Core_Model_Session::getFormKey()
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFormKey(): void
    {
        static::assertIsString(self::$subject->getFormKey());
    }

    /**
     * @covers Mage_Adminhtml_Block_Template::isOutputEnabled()
     * @dataProvider provideIsOutputEnabled
     * @group Block
     */
    public function testIsOutputEnabled(bool $expectedResult, ?string $moduleName): void
    {
        static::assertSame($expectedResult, self::$subject->isOutputEnabled($moduleName));
    }

    /**
     * @group Block
     */
    public function testGetModuleName(): void
    {
        static::assertSame('Mage_Adminhtml', self::$subject->getModuleName());
    }

    /**
     * @see Mage_Core_Model_Input_Filter_MaliciousCode::filter()
     * @group Block
     */
    public function testMaliciousCodeFilter(): void
    {
        static::assertIsString(self::$subject->maliciousCodeFilter(''));
    }
}
