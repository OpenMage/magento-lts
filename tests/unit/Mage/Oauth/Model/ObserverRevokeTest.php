<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Oauth\Model;

use Iterator;
use Mage_Admin_Model_User;
use Mage_Oauth_Model_Observer as Subject;
use Mage_Oauth_Model_Resource_Token;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Event;
use Varien_Event_Observer;
use Varien_Object;

/**
 * Deactivating an admin user revokes the user's OAuth tokens (HackerOne #4084309)
 */
final class ObserverRevokeTest extends OpenMageTest
{
    /**
     * @dataProvider provideSavedObjects
     * @group Model
     */
    public function testRevokeTokensOfDeactivatedAdmin(bool $expectRevoke, ?array $userData, string $class = Mage_Admin_Model_User::class): void
    {
        $resource = $this->createMock(Mage_Oauth_Model_Resource_Token::class);
        $resource->expects($expectRevoke ? self::once() : self::never())
            ->method('revokeAdminTokens')
            ->with(7)
            ->willReturn(1);

        $subject = $this->createPartialMock(Subject::class, ['_getTokenResource']);
        $subject->method('_getTokenResource')->willReturn($resource);

        $object = $this->createPartialMock($class, []);
        $object->setData($userData ?? []);

        $subject->revokeTokensOfDeactivatedAdmin(
            new Varien_Event_Observer(['event' => new Varien_Event(['object' => $object])]),
        );
    }

    /**
     * @return Iterator<string, array{0: bool, 1: (null|array<string, mixed>), 2?: class-string}>
     */
    public static function provideSavedObjects(): Iterator
    {
        yield 'deactivated' => [true, ['user_id' => 7, 'is_active' => 0]];
        yield 'deactivated, string value' => [true, ['user_id' => 7, 'is_active' => '0']];
        yield 'active' => [false, ['user_id' => 7, 'is_active' => '1']];
        yield 'is_active not part of the save' => [false, ['user_id' => 7]];
        yield 'new user' => [false, ['is_active' => 0]];
        yield 'other object' => [false, ['id' => 7, 'is_active' => 0], Varien_Object::class];
    }
}
