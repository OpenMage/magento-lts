<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Block
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter as Subject;
use Mage_Customer_Model_Customer;
use PHPUnit\Framework\TestCase;

class NewsletterTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = new Subject();
    }

    
    public function testInitForm(): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods(['getRegistryCurrentCustomer'])
            ->getMock();

        $mock
            ->method('getRegistryCurrentCustomer')
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            ->willReturn(new Mage_Customer_Model_Customer());

        $this->assertInstanceOf(Subject::class, $mock->initForm());
    }
}
