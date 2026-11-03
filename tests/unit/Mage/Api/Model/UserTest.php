<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model;

use Iterator;
use Mage;
use Mage_Api_Model_User as Subject;
use ReflectionMethod;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model\UserTrait;

/**
 * @phpstan-import-type ValidateData from UserTrait
 */
final class UserTest extends OpenMageTest
{
    use UserTrait;

    private const CURRENT_API_KEY = 'current-api-key';

    private const CURRENT_API_KEY_HASH = '__hash_of_current_api_key__';

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

    /**
     * Saving an API user must invalidate its sessions when access is taken away
     * (deactivation, a different API key), and only then (HackerOne #4084309).
     *
     * @dataProvider provideApiAccessRevokedBySaveData
     * @group Model
     * @param array<string, mixed> $origData
     * @param array<string, mixed> $data
     */
    public function testIsApiAccessRevokedBySave(bool $expectedResult, array $origData, array $data): void
    {
        // The stored hash of the current key; providers run before Mage::app() is available.
        $currentHash = Mage::helper('core')->getHash(self::CURRENT_API_KEY, 32);
        $replaceHash = static fn(array $values): array => array_map(
            static fn($value) => $value === self::CURRENT_API_KEY_HASH ? $currentHash : $value,
            $values,
        );

        $subject = new Subject();
        foreach ($replaceHash($origData) as $key => $value) {
            $subject->setOrigData($key, $value);
        }

        $subject->addData($replaceHash($data));

        $method = new ReflectionMethod($subject, '_isApiAccessRevokedBySave');
        self::assertSame($expectedResult, $method->invoke($subject));
    }

    /**
     * @return Iterator<string, array{bool, array<string, mixed>, array<string, mixed>}>
     */
    public static function provideApiAccessRevokedBySaveData(): Iterator
    {
        $existing = ['user_id' => 7, 'api_key' => self::CURRENT_API_KEY_HASH, 'is_active' => '1'];
        yield 'new user' => [
            false,
            [],
            ['api_key' => self::CURRENT_API_KEY, 'is_active' => '1'],
        ];
        yield 'unchanged existing user' => [
            false,
            $existing,
            ['user_id' => 7, 'firstname' => 'Api', 'is_active' => '1'],
        ];
        yield 'is_active not part of the save' => [
            false,
            $existing,
            ['user_id' => 7, 'reload_acl_flag' => '0'],
        ];
        yield 'deactivated' => [
            true,
            $existing,
            ['user_id' => 7, 'is_active' => '0'],
        ];
        yield 'new API key' => [
            true,
            $existing,
            ['user_id' => 7, 'is_active' => '1', 'new_api_key' => 'rotated-api-key'],
        ];
        yield 'same API key re-saved, e.g. hash upgrade on login' => [
            false,
            $existing,
            ['user_id' => 7, 'api_key' => self::CURRENT_API_KEY_HASH, 'is_active' => '1', 'new_api_key' => self::CURRENT_API_KEY],
        ];
    }
}
