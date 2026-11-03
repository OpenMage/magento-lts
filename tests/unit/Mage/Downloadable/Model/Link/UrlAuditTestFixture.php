<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\Link;

use Mage_Downloadable_Helper_Download;
use Mage_Downloadable_Model_Link_UrlAudit;
use Override;

/**
 * Uses the given download helper and records admin inbox messages instead of saving them
 */
final class UrlAuditTestFixture extends Mage_Downloadable_Model_Link_UrlAudit
{
    /**
     * @var list<array{0: string, 1: string}>
     */
    public array $messages = [];

    public function __construct(private readonly Mage_Downloadable_Helper_Download $helper) {}

    /**
     * @param array{refused: array<string, string>, private: array<string, string>} $report
     */
    public function notify(array $report): void
    {
        $this->_notify($report);
    }

    #[Override]
    protected function _getDownloadHelper(): Mage_Downloadable_Helper_Download
    {
        return $this->helper;
    }

    #[Override]
    protected function _addInboxMessage(string $title, string $description): void
    {
        $this->messages[] = [$title, $description];
    }
}
