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
use RuntimeException;

final class FailingIndexer extends Mage_Index_Model_Indexer_Abstract
{
    public const MESSAGE = 'Test indexer failure';

    #[Override]
    public function getName()
    {
        return 'Failing test indexer';
    }

    #[Override]
    public function reindexAll(): never
    {
        throw new RuntimeException(self::MESSAGE);
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
