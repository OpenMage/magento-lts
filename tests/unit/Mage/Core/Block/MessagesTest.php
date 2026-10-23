<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Block;

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

    /**
     * @dataProvider provideMessageTextAndForbiddenSubstrings
     * @group Block
     */
    public function testMessageHtmlIsSanitized(string $messageText, string $forbiddenSubstring): void
    {
        foreach ($this->renderAllWays($messageText) as $method => $html) {
            self::assertStringContainsString('-msg', $html, "{$method}() rendered no message");
            self::assertStringNotContainsString(
                $forbiddenSubstring,
                $html,
                "{$method}() includes unsanitized '{$forbiddenSubstring}' from malicious payload",
            );
        }
    }

    /**
     * @dataProvider provideMessageTextAndPreservedMarkup
     * @group Block
     */
    public function testMessageMarkupUsedByCoreIsPreserved(string $messageText, string $expectedSubstring): void
    {
        foreach ($this->renderAllWays($messageText) as $method => $html) {
            self::assertStringContainsString($expectedSubstring, $html, "{$method}() altered legitimate markup");
        }
    }

    /**
     * @group Block
     */
    public function testEscapeFlagEscapesEverything(): void
    {
        $subject = new Subject();
        $subject->setEscapeMessageFlag(true);
        $subject->addMessage(new Mage_Core_Model_Message_Error('<a href="https://example.com/">x</a>'));

        self::assertStringContainsString('&lt;a href=&quot;https://example.com/&quot;&gt;x&lt;/a&gt;', $subject->getGroupedHtml());
    }

    /**
     * Render one message of every type through each public rendering method, on a fresh block
     * each time, because getGroupedHtml() (and so toHtml()) clears the collection.
     *
     * @return array<string, string>
     */
    private function renderAllWays(string $messageText): array
    {
        $rendered = [];
        foreach (['toHtml', 'getHtml', 'getGroupedHtml'] as $method) {
            $subject = new Subject();
            $subject->addMessage(new Mage_Core_Model_Message_Error($messageText));
            $subject->addMessage(new Mage_Core_Model_Message_Notice($messageText));
            $subject->addMessage(new Mage_Core_Model_Message_Success($messageText));
            $subject->addMessage(new Mage_Core_Model_Message_Warning($messageText));
            $rendered[$method] = $subject->{$method}();
        }

        return $rendered;
    }
}
