<?php

declare(strict_types=1);

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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_String;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    /**
     * @var Mage_Core_Helper_String
     */
    public Mage_Core_Helper_String $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/string');
    }

    /**
     * @group Mage_Core
     */
    public function testSubstr(): void
    {
        $resultString = $this->subject->substr(self::TEST_STRING, 2, 2);
        $this->assertEquals('34', $resultString);
    }

    /**
     * @group Mage_Core
     */
    public function testTruncate(): void
    {
        $resultString = $this->subject->truncate(self::TEST_STRING, 5, '...');
        $this->assertEquals('12...', $resultString);
    }

    /**
     * @group Mage_Core
     */
    public function testStrlen(): void
    {
        $this->assertEquals(10, $this->subject->strlen(self::TEST_STRING));
    }
}
