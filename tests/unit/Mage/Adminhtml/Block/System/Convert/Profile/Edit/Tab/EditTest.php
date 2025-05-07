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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Convert\Profile\Edit\Tab;

use Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit as Subject;
use Mage_Dataflow_Model_Profile;
use OpenMage\Tests\Unit\OpenMageTest;

class EditTest extends OpenMageTest
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
