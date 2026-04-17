<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Url;

use Mage;
use Mage_Core_Model_Url_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\UrlTrait;

final class ValidatorTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/url_validator');
    }

    /**
     * @dataProvider provideUrl
     * @group Model
     */
    public function testIsValid(bool $expected, string $url): void
    {
        self::assertSame($expected, self::$subject->isValid($url));

        if (!$expected) {
            $messages = self::$subject->getMessages();
            self::assertIsArray($messages);
            self::assertArrayHasKey(Subject::INVALID_URL, $messages);
            self::assertStringContainsString($url, (string) $messages[Subject::INVALID_URL]);
        }
    }
}
