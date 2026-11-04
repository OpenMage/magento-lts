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
use Override;

/**
 * Resolves host names from $dns instead of DNS and records every lookup
 */
final class UrlAuditTestHelperFixture extends Mage_Downloadable_Helper_Download
{
    /**
     * @var array<string, list<string>>
     */
    public array $dns = [];

    /**
     * @var list<string>
     */
    public array $lookups = [];

    #[Override]
    protected function _resolveHost($host)
    {
        $this->lookups[] = $host;
        return $this->dns[$host] ?? [];
    }
}
