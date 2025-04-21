<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_Customer_Edit_Tab_Addresses as Subject;
use Mage_Customer_Model_Customer;
use PHPUnit\Framework\TestCase;

class AddressesTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testInitForm(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getRegistryCurrentCustomer', 'isReadonly'])
            ->getMock();

        $mock
            ->method('getRegistryCurrentCustomer')
            ->willReturn(new Mage_Customer_Model_Customer());

        $mock
            ->method('isReadonly')
            ->willReturn(true);

        $this->assertInstanceOf(Subject::class, $mock->initForm());
    }
}
