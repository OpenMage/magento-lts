<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Exception;
use Mage;
use Mage_Admin_Model_Variable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\VariableTrait;

final class VariableTest extends OpenMageTest
{
    use VariableTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/variable');
    }

    /**
     * @dataProvider provideValidateAdminVariableData
     * @group Model
     * @throws Exception
     */
    public function testValidate(bool|array $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
    }

    public function testIsPathAllowed(): void
    {
        self::assertIsBool(self::$subject->isPathAllowed('invalid-path'));
    }
}
