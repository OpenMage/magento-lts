<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Variable;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
    public Mage_Core_Model_Variable $subject;

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
