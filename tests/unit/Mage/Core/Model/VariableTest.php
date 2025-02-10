<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Core
 * @group Mage_Core_Model
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

    
    public function testGetVariablesOptionArray(): void
    {
        $this->assertIsArray($this->subject->getVariablesOptionArray());
    }
}
