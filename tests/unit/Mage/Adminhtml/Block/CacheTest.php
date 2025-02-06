<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block;

use Mage;
use Mage_Adminhtml_Block_Cache as Subject;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
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
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFlushStorageUrl(): void
    {
        $this->assertStringStartsWith('http', $this->subject->getFlushStorageUrl());
    }

    /**
     * @group Mage_Adminhtml
     * @group Mage_Adminhtml_Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFlushSystemUrl(): void
    {
        $this->assertStringStartsWith('http', $this->subject->getFlushSystemUrl());
    }
}
