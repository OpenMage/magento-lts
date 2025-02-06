<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Generator;
use Mage;
use Mage_Core_Helper_Purifier as Subject;
use PHPUnit\Framework\TestCase;

class PurifierTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/purifier');
    }

    /**
     * @dataProvider providePurify
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testPurify($expectedResult, $content): void
    {
        $this->assertSame($expectedResult, $this->subject->purify($content));
    }

    public function providePurify(): Generator
    {
        yield 'array' => [
            [],
            [],
        ];
        yield 'string' => [
            '',
            '',
        ];
    }
}
