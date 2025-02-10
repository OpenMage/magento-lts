<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Customer
 * @group Mage_Customer_Model
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model;

use Mage;
use Mage_Customer_Model_Customer as Subject;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('customer/customer');
    }

    
    public function testValidateAddress(): void
    {
        $data = [];
        $this->assertIsBool($this->subject->validateAddress($data));
    }
}
