<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Uploader_Helper_Data as Subject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public Subject $subject;

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
