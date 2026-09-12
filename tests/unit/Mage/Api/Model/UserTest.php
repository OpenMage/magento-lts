<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model;

use Mage_Api_Model_User as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model\UserTrait;

/**
 * @phpstan-import-type ValidateData from UserTrait
 */
final class UserTest extends OpenMageTest
{
    use UserTrait;

    /**
     * @dataProvider provideValidateApiUserData
     * @param array|true $expectedResult
     * @phpstan-param ValidateData $data
     * @group Model
     */
    public function testValidate(array|bool $expectedResult, array $data, bool $userExists): void
    {
        $mock = $this->createPartialMock(Subject::class, ['userExists']);
        $mock->setData($data);
        $mock->method('userExists')->willReturn($userExists);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
    }
}
