<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
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

    /**
     * @group Mage_Customer
     * @group Mage_Customer_Model
     */
    public function testValidateAddress(): void
    {
        $data = [];
        $this->assertIsBool($this->subject->validateAddress($data));
    }
}
