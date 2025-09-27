<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Block\Text;

use Mage;
use OpenMage\Tests\Unit\OpenMageTest;

final class ListTest extends OpenMageTest
{
    /**
     * @group Block
     */
    public function testDuplicateBlockName(): void
    {
        $layout = Mage::getModel('core/layout');

        $parentBlock = $layout->createBlock('core/text_list', 'parent');

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A1');
        $parentBlock->append($childBlockA);

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A2');
        $parentBlock->append($childBlockA);

        static::assertSame('A2', $parentBlock->toHtml());
    }

    /**
     * @group Block
     */
    public function testDuplicateBlockNameOrdering(): void
    {
        $layout = Mage::getModel('core/layout');

        $parentBlock = $layout->createBlock('core/text_list', 'parent');

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A');
        $parentBlock->append($childBlockA);

        $childBlockB = $layout->createBlock('core/text', 'child_b')->setText('B');
        $parentBlock->append($childBlockB);

        $childBlockC = $layout->createBlock('core/text', 'child_c')->setText('C');
        $parentBlock->append($childBlockC);

        $parentBlock->unsetChild('child_b');

        $childBlockB = $layout->createBlock('core/text', 'child_b')->setText('B');
        $parentBlock->insert($childBlockB, 'child_c', false);

        static::assertSame('ABC', $parentBlock->toHtml());
    }

    /**
     * @group Block
     */
    public function testUniqueBlockNameOrdering(): void
    {
        $layout = Mage::getModel('core/layout');

        $parentBlock = $layout->createBlock('core/text_list', 'parent');

        $childBlockD = $layout->createBlock('core/text', 'child_d')->setText('D');
        $parentBlock->insert($childBlockD, 'child_c', true);

        $childBlockC = $layout->createBlock('core/text', 'child_c')->setText('C');
        $parentBlock->insert($childBlockC, 'child_b', true);

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A');
        $parentBlock->insert($childBlockA, 'child_b', false);

        $childBlockB = $layout->createBlock('core/text', 'child_b')->setText('B');
        $parentBlock->insert($childBlockB, 'child_a', true);

        $parentBlock->unsetChild('child_a');
        $parentBlock->unsetChild('child_b');

        static::assertSame('CD', $parentBlock->toHtml());
    }

    public function testSortInstructionsAfterReplaceChild(): void
    {
        $layout = Mage::getModel('core/layout');

        $parentBlock = $layout->createBlock('core/text_list', 'parent');

        $childBlockA = $layout->createBlock('core/text', 'target_block')->setText('A');
        $parentBlock->insert($childBlockA, '', false, 'child');

        $childBlockB = $layout->createBlock('core/text', 'target_block')->setText('B');

        // Replacing the block but keeping its order within the parent
        $layout->unsetBlock('target_block');
        $layout->setBlock('target_block', $childBlockB);
        $parentBlock->setChild('child', $childBlockB);

        static::assertSame('B', $parentBlock->toHtml());
    }
}
