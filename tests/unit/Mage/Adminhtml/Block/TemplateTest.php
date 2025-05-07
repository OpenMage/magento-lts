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
