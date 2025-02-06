<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Variable as Subject;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/variable');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testGetVariablesOptionArray(): void
    {
        $this->assertIsArray($this->subject->getVariablesOptionArray());
    }
}
