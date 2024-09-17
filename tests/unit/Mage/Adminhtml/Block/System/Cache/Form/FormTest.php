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

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\System\Cache\Form;

use Mage;
use Mage_Adminhtml_Block_System_Cache_Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @var Mage_Adminhtml_Block_System_Cache_Form
     */
    public Mage_Adminhtml_Block_System_Cache_Form $subject;

    public function setUp(): void
    {
        Mage::app();
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Adminhtml_Block_System_Cache_Form();
    }

    /**
     * @return void
     *
     * @group Mage_Adminhtml
     */
    public function testInitForm(): void
    {
        $this->assertInstanceOf(Mage_Adminhtml_Block_System_Cache_Form::class, $this->subject->initForm());
    }
}
