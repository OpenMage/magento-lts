<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Email;

use Mage;
use Mage_Core_Model_Email_Queue;
use Mage_Core_Model_Email_Template as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;

final class TemplateReturnPathTest extends OpenMageTest
{
    private const SENDER_EMAIL = 'sales@example.com';

    private const RETURN_PATH_EMAIL = 'bounce@example.com';

    /** @var array<string, null|string> */
    private array $originalConfig = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach ([Subject::XML_PATH_SENDING_SET_RETURN_PATH, Subject::XML_PATH_SENDING_RETURN_PATH_EMAIL] as $path) {
            $this->originalConfig[$path] = Mage::getStoreConfig($path);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->originalConfig as $path => $value) {
            Mage::app()->getStore()->setConfig($path, $value);
        }

        parent::tearDown();
    }

    /**
     * Config values are stored and read as strings, so the match must not compare them strictly against ints.
     */
    public static function provideReturnPathConfig(): iterable
    {
        yield 'no' => ['0', null];
        yield 'yes, sender email' => ['1', self::SENDER_EMAIL];
        yield 'specified email' => ['2', self::RETURN_PATH_EMAIL];
    }

    /**
     * @group Model
     */
    #[DataProvider('provideReturnPathConfig')]
    public function testSendUsesConfiguredReturnPath(string $setReturnPath, ?string $expected): void
    {
        $store = Mage::app()->getStore();
        $store->setConfig(Subject::XML_PATH_SENDING_SET_RETURN_PATH, $setReturnPath);
        $store->setConfig(Subject::XML_PATH_SENDING_RETURN_PATH_EMAIL, self::RETURN_PATH_EMAIL);

        $queue = $this->getMockWithCalledMethods(Mage_Core_Model_Email_Queue::class, [
            'addMessageToQueue' => self::WILL_RETURN_SELF,
        ]);

        $subject = $this->getMockWithCalledMethods(Subject::class, [
            'isValidForSend'              => true,
            'getProcessedTemplate'        => 'body',
            'getProcessedTemplateSubject' => 'subject',
        ]);
        $subject->setSenderEmail(self::SENDER_EMAIL);
        $subject->setQueue($queue);

        self::assertTrue($subject->send('customer@example.com', 'Customer'));
        self::assertSame($expected, $queue->getMessageParameters()['return_path_email']);
    }
}
