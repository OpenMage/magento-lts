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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Generator;
use Mage;
use Mage_Core_Helper_Purifier;
use PHPUnit\Framework\TestCase;

class PurifierTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    public Mage_Core_Helper_Purifier $subject;

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
