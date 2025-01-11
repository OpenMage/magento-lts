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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab;

use Mage;
use Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter;
use Mage_Customer_Model_Customer;
use PHPUnit\Framework\TestCase;

class NewsletterTest extends TestCase
{
    public Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter();
    }

    /**
     *
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     */
    public function testInitForm(): void
    {
        $mock = $this->getMockBuilder(Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter::class)
            ->setMethods(['getRegistryCurrentCustomer'])
            ->getMock();

        $mock
            ->method('getRegistryCurrentCustomer')
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            ->willReturn(new Mage_Customer_Model_Customer());

        $this->assertInstanceOf(Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter::class, $mock->initForm());
    }
}
