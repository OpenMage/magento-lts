<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\ImportExport\Block\Adminhtml\Import\Frame;

use Mage_Core_Block_Messages;
use Mage_Core_Model_Layout;
use Mage_ImportExport_Block_Adminhtml_Import_Frame_Result as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\ImportExport\Block\Adminhtml\Import\Frame\ResultTrait;

final class ResultTest extends OpenMageTest
{
    use ResultTrait;

    private const IMPORT_START_URL = 'https://example.com/admin/import/start/';

    /**
     * The messages block purifies message text, which must not remove the Import button the block appends.
     *
     * @group Block
     */
    public function testGetMessagesHtmlKeepsImportButton(): void
    {
        $subject = $this->getSubject();
        $subject->addSuccess('File is valid! To start import process press "Import" button', true);

        $html = $subject->getMessagesHtml();

        self::assertStringContainsString($subject->getImportButtonHtml(), $html);
        self::assertStringNotContainsString('&lt;button', $html);
    }

    /**
     * @group Block
     */
    public function testGetMessagesHtmlStillPurifiesMessageText(): void
    {
        $subject = $this->getSubject();
        $subject->addError('Row 1: <script>alert(1)</script><button onclick="alert(2)">x</button>');
        $subject->addSuccess('File is valid!', true);

        $html = $subject->getMessagesHtml();

        self::assertStringNotContainsString('<script>', $html);
        self::assertStringNotContainsString('<button onclick="alert(2)"', $html);
        self::assertSame(1, substr_count($html, '<button'));
    }

    private function getSubject(): Subject
    {
        $layout = $this->createMock(Mage_Core_Model_Layout::class);
        $layout->method('createBlock')->willReturnCallback(static fn() => new Mage_Core_Block_Messages());

        $subject = $this->getMockWithCalledMethods(Subject::class, [
            'getImportStartUrl' => self::IMPORT_START_URL,
        ]);
        $subject->setLayout($layout);

        return $subject;
    }
}
