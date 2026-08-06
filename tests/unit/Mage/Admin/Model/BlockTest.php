<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Override;
use Exception;
use Mage;
use Mage_Admin_Model_Block as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\BlockTrait;

/**
 * @phpstan-import-type ValidateData from BlockTrait
 */
final class BlockTest extends OpenMageTest
{
    use BlockTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/block');
    }

    /**
     * @dataProvider provideValidateAdminBlockData
     * @param bool|string[] $expectedResult
     * @phpstan-param ValidateData    $data
     *
     * @group Model
     * @throws Exception
     */
    public function testValidate(array|bool $expectedResult, array $data): void
    {
        self::$subject->setData($data);
        self::assertEquals($expectedResult, self::$subject->validate());
    }

    /**
     * @group Model
     */
    public function testIsTypeAllowed(): void
    {
        self::assertIsBool(self::$subject->isTypeAllowed('invalid-type'));
    }
}
