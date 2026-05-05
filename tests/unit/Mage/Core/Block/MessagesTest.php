<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Block;

use Override;
use Mage_Core_Block_Messages as Subject;
use Mage_Core_Model_Message_Error;
use Mage_Core_Model_Message_Notice;
use Mage_Core_Model_Message_Success;
use Mage_Core_Model_Message_Warning;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Block\MessagesTrait;

final class MessagesTest extends OpenMageTest
{
    use MessagesTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * @dataProvider provideMessageTextAndForbiddenSubstrings
     * @group Block
     */
    public function testMessageHtmlIsSanitized(string $messageText, string $forbiddenSubstring): void
    {
        self::$subject->addMessage(new Mage_Core_Model_Message_Error($messageText));
        self::$subject->addMessage(new Mage_Core_Model_Message_Notice($messageText));
        self::$subject->addMessage(new Mage_Core_Model_Message_Success($messageText));
        self::$subject->addMessage(new Mage_Core_Model_Message_Warning($messageText));

        self::assertStringNotContainsString(
            $forbiddenSubstring,
            self::$subject->toHtml(),
            "Message output toHtml() includes unsanitized '{$forbiddenSubstring}' from malicious payload",
        );

        self::assertStringNotContainsString(
            $forbiddenSubstring,
            self::$subject->getHtml(),
            "Message output getHtml() includes unsanitized '{$forbiddenSubstring}' from malicious payload",
        );

        self::assertStringNotContainsString(
            $forbiddenSubstring,
            self::$subject->getGroupedHtml(),
            "Message output getHtml() includes unsanitized '{$forbiddenSubstring}' from malicious payload",
        );
    }
}
