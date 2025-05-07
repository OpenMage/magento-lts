<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block;

use Mage_Adminhtml_Block_Cache as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class CacheTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = new Subject();
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFlushStorageUrl(): void
    {
        static::assertStringStartsWith('http', self::$subject->getFlushStorageUrl());
    }

    /**
     * @group Block
     * @group runInSeparateProcess
     * @runInSeparateProcess
     */
    public function testGetFlushSystemUrl(): void
    {
        static::assertStringStartsWith('http', self::$subject->getFlushSystemUrl());
    }
}
