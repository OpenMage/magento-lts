<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Block\Text;

use Mage;
use PHPUnit\Framework\TestCase;

class ListTest extends TestCase
{
    public function setUp(): void
    {
        Mage::app();
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Block
     */
    public function testDuplicateBlockName(): void
    {
        $layout = Mage::getModel('core/layout');

        $parentBlock = $layout->createBlock('core/text_list', 'parent');

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A1');
        $parentBlock->append($childBlockA);

        $childBlockA = $layout->createBlock('core/text', 'child_a')->setText('A2');
        $parentBlock->append($childBlockA);

        $this->assertSame('A2', $parentBlock->toHtml());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Block
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

        $this->assertSame('ABC', $parentBlock->toHtml());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Block
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

        $this->assertSame('CD', $parentBlock->toHtml());
    }

    public function testSortInstructionsAfterReplaceChild()
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

        $this->assertSame('B', $parentBlock->toHtml());
    }
}
