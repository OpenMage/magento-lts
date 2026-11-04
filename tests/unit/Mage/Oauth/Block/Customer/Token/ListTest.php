<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Oauth\Block\Customer\Token;

use Mage;
use Mage_Core_Model_Session;
use Mage_Oauth_Block_Customer_Token_List as Subject;
use Mage_Oauth_Model_Token;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Block\Customer\Token\ListTrait;
use Override;
use ReflectionClass;

final class ListTest extends OpenMageTest
{
    use ListTrait;

    private const FORM_KEY = 'TestFormKey123';

    #[Override]
    protected function setUp(): void
    {
        $coreSession = $this->createPartialMock(Mage_Core_Model_Session::class, ['getFormKey']);
        $coreSession->method('getFormKey')->willReturn(self::FORM_KEY);
        Mage::unregister('_singleton/core/session');
        Mage::register('_singleton/core/session', $coreSession);
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_singleton/core/session');
    }

    /**
     * The revoke/enable and delete links must carry the session form key, the controller rejects
     * requests without it. Without it a cross-site GET could re-enable an application the customer
     * had revoked.
     *
     * @group Block
     */
    public function testActionLinksCarryFormKey(): void
    {
        // The constructor loads the customer's token collection, which needs a database
        $subject = (new ReflectionClass(Subject::class))->newInstanceWithoutConstructor();
        $token = $this->getMockWithCalledMethods(Mage_Oauth_Model_Token::class, ['getId' => 5]);
        $token->setRevoked(1);

        $revoke = $subject->getUpdateRevokeLink($token);
        self::assertStringContainsString('oauth/customer_token/revoke/', $revoke);
        self::assertStringContainsString('/id/5/', $revoke);
        self::assertStringContainsString('/status/0/', $revoke);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $revoke);

        $delete = $subject->getDeleteLink($token);
        self::assertStringContainsString('oauth/customer_token/delete/', $delete);
        self::assertStringContainsString('/id/5/', $delete);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $delete);
    }
}
