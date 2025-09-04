<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Convert\Gui\Edit\Tab;

use Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_View as Subject;
use Mage_Dataflow_Model_Profile;
use OpenMage\Tests\Unit\OpenMageTest;

final class ViewTest extends OpenMageTest
{
    /**
     * @group Block
     */
    public function testInitForm(): void
    {
        $methods = [
            'getRegistryCurrentConvertProfile' => new Mage_Dataflow_Model_Profile(),
        ];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertInstanceOf(Subject::class, $mock->initForm());
    }
}
