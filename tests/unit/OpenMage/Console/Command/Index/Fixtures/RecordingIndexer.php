<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Index\Fixtures;

use Mage_Index_Model_Event;
use Mage_Index_Model_Indexer_Abstract;
use Override;

/**
 * Indexer that only counts its full reindexes
 */
final class RecordingIndexer extends Mage_Index_Model_Indexer_Abstract
{
    public static int $reindexCount = 0;

    #[Override]
    public function getName()
    {
        return 'Recording test indexer';
    }

    #[Override]
    public function reindexAll(): void
    {
        ++self::$reindexCount;
    }

    #[Override]
    protected function _registerEvent(Mage_Index_Model_Event $event): static
    {
        return $this;
    }

    #[Override]
    protected function _processEvent(Mage_Index_Model_Event $event): static
    {
        return $this;
    }
}
