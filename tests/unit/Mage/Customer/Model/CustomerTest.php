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
