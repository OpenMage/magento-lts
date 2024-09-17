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

use Mage;
use Mage_Core_Helper_Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public const TEST_URL_1 = 'http://example.com?foo=ba';

    public const TEST_URL_2 = 'http://example.com?foo=bar&boo=baz';

    public const TEST_URL_PUNY = 'http://XN--example.com?foo=bar&boo=baz';

    /**
     * @var Mage_Core_Helper_Url
     */
    public Mage_Core_Helper_Url $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/url');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetCurrentBase64Url(): void
    {
        $this->assertIsString($this->subject->getCurrentBase64Url());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetEncodedUrl(): void
    {
        $this->assertIsString($this->subject->getEncodedUrl());
        $this->assertIsString($this->subject->getEncodedUrl(self::TEST_URL_1));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetHomeUrl(): void
    {
        $this->assertIsString($this->subject->getHomeUrl());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testAddRequestParam(): void
    {
        $this->assertIsString($this->subject->addRequestParam(self::TEST_URL_1, [0 => 'int']));
        $this->assertIsString($this->subject->addRequestParam(self::TEST_URL_1, ['null' => null]));
        $this->assertIsString($this->subject->addRequestParam(self::TEST_URL_1, ['key' => 'value']));
        $this->assertIsString($this->subject->addRequestParam(self::TEST_URL_1, ['key' => ['subKey' => 'subValue']]));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testRemoveRequestParam(): void
    {
        $this->assertIsString($this->subject->removeRequestParam(self::TEST_URL_1, 'foo'));
        $this->assertIsString($this->subject->removeRequestParam(self::TEST_URL_2, 'foo'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testEncodePunycode(): void
    {
        $this->assertIsString($this->subject->encodePunycode(self::TEST_URL_1));
        $this->assertIsString($this->subject->encodePunycode(self::TEST_URL_PUNY));
    }
    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testDecodePunycode(): void
    {
        $this->assertIsString($this->subject->decodePunycode(self::TEST_URL_1));
        $this->assertIsString($this->subject->decodePunycode(self::TEST_URL_PUNY));
    }
}
