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
use Mage_Admin_Model_Block as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\BlockTrait;

final class BlockTest extends OpenMageTest
{
    use BlockTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/block');
    }

    /**
     * @dataProvider provideValidateAdminBlockData
     * @param true|array<int, string> $expectedResult
     *
     * @group Model
     * @throws Exception
     */
    public function testValidate(bool|array $expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertEquals($expectedResult, $mock->validate());
    }

    /**
     * @group Model
     */
    public function testIsTypeAllowed(): void
    {
        self::assertIsBool(self::$subject->isTypeAllowed('invalid-type'));
    }
}
