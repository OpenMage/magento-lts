<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Mage\Oauth\Model;

use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Oauth_Model_Token as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Oauth\Model\TokenTrait;

final class TokenTest extends OpenMageTest
{
    use TokenTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/token');
    }

    /**
     * @dataProvider provideValidateData
     * @group Model
     * @param array<string, string> $methods
     */
    public function testValidate(bool|string $expected, array $methods): void
    {
        self::$subject->setConsumerId($methods['setConsumerId']);
        self::$subject->setCallbackUrl($methods['setCallbackUrl']);
        self::$subject->setSecret($methods['setSecret']);
        self::$subject->setToken($methods['setToken']);
        self::$subject->setVerifier($methods['setVerifier']);

        try {
            self::assertTrue(self::$subject->validate());
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame($expected, $mageCoreException->getMessage());
        }
    }
}
