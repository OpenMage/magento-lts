<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Uploader_Helper_Data;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var Mage_Uploader_Helper_Data
     */
    public Mage_Uploader_Helper_Data $subject;

    public function setUp(): void
    {
        $this->subject = Mage::helper('uploader/data');
    }

    /**
     * @return void
     */
    public function testIsModuleEnabled(): void
    {
        $this->assertIsBool($this->subject->isModuleEnabled());
    }
}
