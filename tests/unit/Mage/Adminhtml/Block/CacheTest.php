<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @group Mage_Adminhtml
 * @group Mage_Adminhtml_Block
 * @group runInSeparateProcess
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

    
    public function testGetFlushStorageUrl(): void
    {
        $this->assertStringStartsWith('http', $this->subject->getFlushStorageUrl());
    }

    
    public function testGetFlushSystemUrl(): void
    {
        $this->assertStringStartsWith('http', $this->subject->getFlushSystemUrl());
    }
}
