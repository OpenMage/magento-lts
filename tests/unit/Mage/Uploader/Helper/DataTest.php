<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Uploader_Helper_Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Mage_Uploader_Helper_Data $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('uploader/data');
    }

    /**
     * @group Mage_Uploader
     * @group Mage_Uploader_Helper
     */
    public function testIsModuleEnabled(): void
    {
        $this->assertIsBool($this->subject->isModuleEnabled());
    }
}
