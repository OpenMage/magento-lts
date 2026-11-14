<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Model\Auth\User;

use Iterator;
use Mage_Admin_Model_User;
use Mage_Api2_Exception;
use Mage_Api2_Model_Auth_User_Admin as Subject;
use Mage_Api2_Model_Server;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;

/**
 * REST access through an admin OAuth token ends when the admin user is deactivated or deleted
 * (HackerOne #4084309)
 */
final class AdminActiveTest extends OpenMageTest
{
    /**
     * @dataProvider provideRefusedAdminUsers
     * @group Model
     * @param array<string, mixed> $userData
     */
    public function testGetRoleRefusesInactiveOrMissingAdminUser(array $userData): void
    {
        $subject = $this->createSubject($userData);

        try {
            $subject->getRole();
            self::fail('Expected Mage_Api2_Exception');
        } catch (Mage_Api2_Exception $mageApi2Exception) {
            self::assertSame(Mage_Api2_Model_Server::HTTP_UNAUTHORIZED, $mageApi2Exception->getCode());
        }
    }

    /**
     * @return Iterator<string, array{array<string, mixed>}>
     */
    public static function provideRefusedAdminUsers(): Iterator
    {
        yield 'deactivated' => [['user_id' => 5, 'is_active' => 0]];
        yield 'deactivated, string value' => [['user_id' => 5, 'is_active' => '0']];
        yield 'deleted' => [[]];
    }

    /**
     * @group Model
     */
    public function testActiveAdminUserPassesTheCheck(): void
    {
        $subject = $this->createSubject(['user_id' => 5, 'is_active' => '1']);

        (new ReflectionMethod(Subject::class, '_assertAdminUserIsActive'))->invoke($subject);

        self::addToAssertionCount(1);
    }

    /**
     * @param array<string, mixed> $userData
     */
    private function createSubject(array $userData): Subject
    {
        $user = $this->createPartialMock(Mage_Admin_Model_User::class, []);
        $user->setData($userData);

        $subject = $this->createPartialMock(Subject::class, ['_loadAdminUser']);
        $subject->expects(self::once())->method('_loadAdminUser')->with(5)->willReturn($user);
        $subject->setUserId(5);

        return $subject;
    }
}
