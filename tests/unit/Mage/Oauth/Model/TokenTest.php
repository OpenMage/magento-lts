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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Mage\Oauth\Model;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Oauth_Model_Token as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class TokenTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('oauth/token');
    }

    /**
     * @dataProvider validateDataProvider
     * @group Model
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

    public function validateDataProvider(): Generator
    {
        $validData = [
            'setConsumerId'     => '1',
            'setCallbackUrl'    => 'https://example.com/callback',
            'setSecret'         => str_repeat('x', 32),
            'setToken'          => str_repeat('x', 32),
            'setVerifier'       => str_repeat('x', 32),
        ];

        $error = 'This value should have exactly 32 characters.';

        yield 'valid' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 3);
        yield 'invalid to short secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setSecret'] = str_repeat('x', 33);
        yield 'invalid to long secret' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setToken'] = str_repeat('x', 3);
        yield 'invalid to short token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setToken'] = str_repeat('x', 33);
        yield 'invalid to long token' => [
            $error,
            $data,
        ];

        $data = $validData;
        $data['setCallbackUrl'] = 'invalid-url';
        yield 'invalid url' => [
            'Invalid URL "invalid-url".',
            $data,
        ];
    }
}
